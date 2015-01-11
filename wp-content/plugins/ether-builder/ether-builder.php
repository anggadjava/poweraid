<?php
/*
Plugin Name: Ether Builder
Plugin URI: http://onether.com/ether-builder
Description: Ether Content Builder WordPress Plugin is a powerful tool for building custom content pages aided with innovative visual composer. It comes equipped with many commonly used widgets that can be laid out via interactive drag&drop interface. Ether Builder can use 3rd party widgets as well as custom crafted ones. Ether Builder is actively developed by Ether and gets regular updates and bug fixes.
Author: ether
Author URI: onether.com
Version: 2.0.5
*/
 
if ( ! class_exists('ether'))
{
	include('ether/ether.php');
}

ether::config('name', 'Ether Builder');
ether::config('version', '2.0.5');
ether::config('debug', FALSE);
ether::config('debug_wordpress', FALSE);
ether::config('debug_sql', FALSE);
ether::config('debug_echo', FALSE);
ether::config('fix_formatting', FALSE);
ether::config('image_frame_class', 'ether-frame ether-frame-1');
ether::config('hide_title', FALSE);
ether::config('register_default_sidebar', FALSE);
ether::config('builder_widget_prefix', 'ether-');
ether::config('builder_archive_hide', TRUE);
ether::config('builder_lightbox', TRUE);
ether::config('online_docs', 'http://onether.com/ether-builder/docs');
ether::config('online_docs', 'http://onether.com/ether-builder/support');

ether::configjs('builder_tab', FALSE);
ether::configjs('builder_lang', array
(
	'quit' => ether::langr('Quit without saving?'),
	'changes' => ether::langr('Lose editor changes?'),
	'sure' => ether::langr('Are you sure?'),
	'unwrap_row' => ether::langr('This will unwrap all widgets nested within this row. This action cannot be undone. Proceed?')
));

ether::init();

ether::depend('wp', '3.1.0');
ether::depend('php', '5.2.3');

ether::depend('function', 'imagecreatetruecolor');
ether::depend('function', 'imagecolorallocatealpha');
ether::depend('function', 'imagecolortransparent');
ether::depend('function', 'json_decode');
ether::depend('function', 'file_get_contents');

ether::depend('function', 'curl_init');
ether::depend('class', 'ZipArchive');

$uploads = wp_upload_dir();

ether::depend(array('directory', 'writable'), $uploads['basedir']);
ether::depend(array('directory', 'writable'), $uploads['basedir'].'/'.ether::config('upload_dir'));

ether::module('contact');
ether::module('builder');
ether::module('update');

ether::admin_panel('Builder', array('title' => ether::langx('Builder', 'admin panel name', TRUE), 'group' => 'Ether'));
ether::admin_panel('Update', array('title' => ether::langx('Update', 'admin panel name', TRUE), 'group' => 'Ether'));
ether::admin_panel('License', array('title' => ether::langx('License', 'admin panel name', TRUE), 'group' => 'Ether'));

add_filter('the_editor', array('ether_builder', 'builder_tab'), 999);
add_filter('the_content', array('ether_builder', 'builder_content'), 999);
add_filter('posts_where_request', array('ether_builder', 'builder_search'));
add_filter('posts_join_request', array('ether_builder', 'builder_search_join'));
add_filter('posts_distinct_request', array('ether_builder', 'builder_search_distinct'));
add_filter('get_post_metadata', array('ether_builder', 'unserialize_fix'), 10, 4);
add_action('template_redirect', array('ether_builder', 'builder_setup'), 999);
add_action('admin_head', array('ether_builder', 'header'));
add_action('wp_head', array('ether_builder', 'header'), 999);
add_action('ether_setup', array('ether_builder', 'builder_sidebar_widgets_init'), 1);
add_action('ether_setup', array('ether_builder', 'widgets_init'), 2);
add_action('ether_setup', array('ether_builder', 'sidebar_builder_widgets_init'), 3);
add_action('wp_restore_post_revision', array('ether_metabox_builder', 'restore_revision'), 10, 2);
add_action('admin_head', array('ether_builder', 'tinymce_quiet_setup'));
add_action('sidebar_admin_page', array('ether_metabox_builder', 'header'));
add_action('admin_footer', array('ether_builder', 'builder_prototypes'));
add_action('admin_footer', array('ether_builder', 'builder_previews'));
add_action('init', 'ether_builder_dashicons');

if ( ! function_exists('ether_builder_init'))
{
	function ether_builder_init()
	{
		$regular_types = array('post', 'page');
		$custom_types = array_keys(get_post_types(array('_builtin' => FALSE, 'public' => TRUE, 'show_ui' => TRUE)));

		$builder_post_types = array_unique(array_merge($regular_types, $custom_types));
		$builder_hidden_types = ether::option('builder_hidden_types');

		if (isset($builder_hidden_types[0]) AND ! empty($builder_hidden_types[0]))
		{
			$builder_hidden_types = $builder_hidden_types[0];
			$trim = array();
			foreach($builder_hidden_types as $key => $val)
			{
				$trim[str_replace('_post_type', '', $key)] = $val;
			}
			$builder_hidden_types = $trim;

			$builder_post_types = array_diff($builder_post_types, array_keys($builder_hidden_types));
		}

		ether::admin_metabox('Builder post', array('title' => ether::langx('Builder', 'metabox name', TRUE), 'permissions' => $builder_post_types, 'context' => 'side'));

		ether::config('builder_archive_hide', ether::option('builder_archive_hide') == 'on');

		//
		$grid_spacing = ether::option('builder_grid_spacing');
		empty($grid_spacing) ? $grid_spacing = 30 : '';
		ether::option('builder_grid_spacing', $grid_spacing);
	}

	add_action('ether_setup', 'ether_builder_init');
}

if ( ! function_exists('ether_builder_custom_css'))
{
	function ether_builder_custom_css()
	{
		if (is_admin())
			return;

		$spacing = ether::option('builder_grid_spacing');
		$styles = '';

		if ($spacing != 30) //30 is a hardcoded val in pregenerated stylesheet
		{
			$grid_spacing_pattern = file_get_contents(ether::dir('/media/stylesheets/grid-spacing-pattern.css', TRUE));
			$half_spacing = $spacing / 2;
			$prefix = str_replace('_', '-', ether::config('prefix'));

			// $styles .= preg_replace(array('/\[\["half_spacing"\]\]/', '/\[\["prefix"\]\]/'), array($half_spacing, $prefix), $grid_spacing_pattern);
			$styles .= preg_replace(array('/\[\["half_spacing"\]\]/', '/\\{\\}(?!\s)/'), array($half_spacing, $prefix), $grid_spacing_pattern);

			echo '<style id="ether-builder-gen-css" type="text/css">'.$styles.'</style>';
		}
	}

	add_action( 'wp_print_scripts', 'ether_builder_custom_css' );
}

if ( ! function_exists('ether_builder_js_defaults'))
{
	function ether_builder_js_defaults() 
	{
		if (is_admin())
			return;

		$props = array(
			'gridslider' => array()
		);

		$grid_spacing = ether::option('builder_grid_spacing');
		! empty($grid_spacing) ? $props['gridslider']['col_spacing_size'] = $grid_spacing : '';

		$script = 'if (typeof ether == \'undefined\') ether = {}; ether.builder_defaults = ether.builder_defaults || {}; ';
		foreach($props as $name => $group)
		{
			$count = count($group);
			$id = 0;
			$script .= 'ether.builder_defaults.gridslider = { ';
			foreach($group as $k => $v)
			{
				$id += 1;
				$script .= $k.': '.$v.(($id < $count) ? ',' : '').' ';
			}
			$script .='}';
		}

		echo '<script type="text/javascript">'.$script.'</script>';
	}
	add_action( 'wp_print_scripts', 'ether_builder_js_defaults' );
}

if ( ! function_exists('ether_builder_header'))
{
	function ether_builder_header()
	{
		$color = ether::option('builder_color');

		if (empty($color))
		{
			$color = 'light';
		}

		ether::stylesheet('ether-builder', 'media/stylesheets/ether-builder-'.$color.'.css', NULL, ether::config('version'));
		ether::stylesheet('ether-builder-ie7', 'media/stylesheets/ether-builder-ie7.css', array('ether-builder'), ether::config('version'), 'IE 7');

		if (ether::config('builder_lightbox'))
		{
			ether::stylesheet('jquery.colorbox', 'media/stylesheets/libs/colorbox/colorbox.css');
			ether::script('jquery.colorbox', 'media/scripts/libs/jquery.colorbox.js', array('jquery'));
		}

		ether::script('ether-builder', 'media/scripts/ether-builder.js', array('jquery', 'jquery.colorbox'), ether::config('version'));

		// $deps = array
		// (
		// 	'core',
		// 	'jquery.easing',
		// 	'jquery.mousewheel',
		// 	'jquery.swipe',
		// 	'utils',
		// 	'image-loader',
		// 	'css-generator',
		// 	'event-manager-2',
		// 	'widget-manager',
		// 	'ether-widget',
		// 	'ether-media-widget-3',
		// 	'ether-dynamic-title-2',
		// 	'ether-grid-slider',
		// 	'ether-tab-widget',
		// 	'ether-builder'
		// );

		// foreach ($deps as $filename)
		// {
		// 	ether::script($filename.'temp', '../../../../ether-styles-generator/ether/'.$filename.'.js', array('jquery', 'jquery.colorbox'), ether::config('version'));
		// 	ether::script($filename.'temp', 'media/scripts/temp/'.$filename.'.js', array('jquery', 'jquery.colorbox'), ether::config('version'));
		// }
	}

	add_action('ether_builder_header', 'ether_builder_header');
}

if ( ! function_exists('ether_builder_dashicons'))
{
	function ether_builder_dashicons()
	{
		if ( ! wp_style_is('dashicons', 'registered'))
		{
			wp_register_style('dashicons', plugins_url('admin/media/stylesheets/dashicons.css', __FILE__ ), FALSE, filemtime(plugin_dir_path(__FILE__).'admin/media/stylesheets/dashicons.css'));
			wp_enqueue_style('dashicons');
		}
	}
}

if ( ! function_exists('ether_builder_backup_config'))
{
	function ether_builder_backup_config()
	{
		if (class_exists('ether_backup'))
		{
			ether_backup::add_url_rule('meta_serialized', 'ether_builder_data');
			ether_backup::add_url_rule('meta_serialized', 'ether_builder_widget_post_data');
		}
	}

	add_action('ether_setup', 'ether_builder_backup_config');
}

?>

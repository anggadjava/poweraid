<?php
/**
 * Ether
 * Wordpress fluid framework
 * http://onether.com
 *
 * License
 * Copyright (c) 2010-2014, Ether. All rights reserved.
 *
 * @author Ether <contact@onether.com>
 * @version 1.7.3
 *
 * @copyright Copyright (c) 2010-2014, Ether. All rights reserved.
 */

defined('ETHER_VERSION') OR define('ETHER_VERSION', '1.7.3');
defined('ETHER_FILE') OR define('ETHER_FILE', __FILE__);
defined('ETHER_EVENT') OR define('ETHER_EVENT', TRUE);

global $ETHER;

$ETHER = array
(
	'ROOT' => dirname(__FILE__).'/',
	'APP_ROOT' => dirname(dirname(__FILE__)).'/',
	'WP_ROOT' => dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/',
	'IS_PLUGIN' => FALSE,
	'IS_THEME' => FALSE
);

if (basename(dirname($ETHER['APP_ROOT'])) == 'plugins')
{
	$ETHER['IS_PLUGIN'] = TRUE;
} else if (basename(dirname($ETHER['APP_ROOT'])) == 'themes')
{
	$ETHER['IS_THEME'] = TRUE;
}

if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME']) AND ! defined('ABSPATH'))
{
	require_once($ETHER['WP_ROOT'].'wp-load.php');

	ether::config('debug', FALSE);
	ether::config('debug_echo', FALSE);
	ether::config('debug_output', FALSE);

	if ( ! empty($_GET))
	{
		ether::trigger('ether.get', array($_GET));
	}

	if ( ! empty($_POST))
	{
		ether::trigger('ether.post', array($_POST));
	}

	if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
	{
		ether::trigger('ether.ajax', array($_GET));
	}

	exit;
}

defined('_n') OR define('_n', "\n");

if ( ! function_exists('_t'))
{
	function _t($t = 0)
	{
		return str_repeat("\t", $t);
	}
}

if ( ! class_exists('ether'))
{
	class ether
	{
		protected static $config = array
		(
			// enable cache for pages (concerns whole page caching)
			'cache' => FALSE,
			// fallback cache lifetime
			'cache_lifetime' => 86400,
			// cache always, even with cache disabled in admin menu (concerns whole page caching)
			'cache_always' => FALSE,
			// cache results from option, meta, info functions
			'cache_data' => FALSE,
			'cache_logged_in' => FALSE,
			'cache_url_exceptions' => array(),
			// root directory
			'root' => '', //$ETHER['ROOT'],
			// plugin/theme directory
			'app_root' => '', //$ETHER['APP_ROOT'],
			// root wordpress directory
			'wp_root' => '', //$ETHER['WP_ROOT'],
			'is_plugin' => FALSE, //$ETHER['IS_PLUGIN'],
			'is_theme' => FALSE, //$ETHER['IS_THEME'],
			// name of component
			'name' => 'Ether',
			// version of component
			'version' => '1.0',
			// default extension for php files
			'ext' => 'php',
			// theme directory name
			'dir' => '', //basename(dirname(dirname(__FILE__))),
			// prefix for database/locale entries
			'prefix' => 'ether_',
			// default sidebar title code
			'sidebar_title' => '<h3>%</h3>',
			// default sidebar container code
			'sidebar_container' => '<div class="section">%</div>',
			// register 3 default sidebars (home, page, post)
			'register_default_sidebar' => TRUE,
			// registter 2 default menus (header, footer)
			'register_menu' => TRUE,
			// use auto paragraph wrapping for images
			'image_autop' => FALSE,
			// some code fixing stupid wordpress formatting behavior
			'fix_formatting' => TRUE,
			// hide custom fields metabox
			'hide_custom_fields' => FALSE,
			// use callback right after theme activation
			'activate_callback' => array('ether', 'action_activate'),
			// use callback right after theme deactivation
			'deactivate_callback' => array('ether', 'action_deactivate'),
			// default directory name for uploads related with framework/theme
			'upload_dir' => 'ether',
			// default url for uploaded files related with framework/theeme
			'upload_url' => '',
			// filter callback for images edited by wordpress tool
			'image_editor_filter' => array('ether', 'filter_image_editor'),
			'image_frame_class' => 'frame frame-1',
			// enable debug
			'debug' => FALSE,
			// read warnings/errors from wordpress
			'debug_wordpress' => FALSE,
			'debug_sql' => FALSE,
			'debug_echo' => FALSE,
			'debug_output' => FALSE,
			// excluded categories from main list
			// if search custom post === TRUE then include all custom post types in search results
			// if is array, then search in specific ones
			'custom_post_search' => FALSE,
			'custom_post_yearly_archive' => FALSE,
			'pagination' => TRUE,
			// special prefix for thumbnails genererated by get_image_base/get_image_thumbnail
			'thumb_prefix' => '-thumb',
			'tump_on_send' => TRUE,
			'deps' => array(),
			'bootstrap_template' => FALSE,
			// checks if tinymce has been initialized
			'has_tinymce' => FALSE,
			// add menu / submenu entries to admin bar
			'register_adminbar' => TRUE
		);

		protected static $configjs = array();
		protected static $log_buffer = array();

		protected static $controller;
		protected static $panels = array();
		protected static $modules = array();
		protected static $panel = '';
		protected static $metaboxes = array();
		protected static $metabox_permissions = array();
		protected static $custom_post_types = array();
		protected static $columns = array();
		protected static $tax_metabox = array();
		protected static $bindings = array();
		protected static $shortcodes = array();
		protected static $quicktags = array();
		protected static $wysiwig = array();
		protected static $csv = array
		(
			'terminated' => "\n",
			'separator' => ',',
			'enclosed' => '"',
			'escaped' => '\\'
		);

		protected static $deps = array();

		protected static $__setup = FALSE;
		protected static $__init = FALSE;
		protected static $__branch = array();

		public static function init()
		{
			$backtrace = debug_backtrace();
			$branch = array_shift($backtrace);
			$name = basename(dirname($branch['file']));

			if (isset($branch['file']) AND ! isset(self::$__branch[$name]))
			{
				$new_branch = array
				(
					'root' => dirname($branch['file']).'/ether/',
					'app_root' => dirname($branch['file']).'/',
					'wp_root' => dirname(WP_CONTENT_DIR).'/',
					'is_plugin' => (basename(dirname(dirname($branch['file']))) == 'plugins' ? TRUE : FALSE),
					'is_theme' => (basename(dirname(dirname($branch['file']))) == 'themes' ? TRUE : FALSE),
					'name' => self::config('name'),
					'version' => self::config('version'),
					'online_docs' => self::config('online_docs'),
					'online_support' => self::config('online_support')
				);

				if ($new_branch['is_theme'])
				{
					$theme_found = FALSE;

					foreach (self::$__branch as $branch => $data)
					{
						if ($data['is_theme'])
						{
							$theme_found = TRUE;
							break;
						}
					}

					if ( ! $theme_found)
					{
						self::$__branch[$name] = $new_branch;
					}
				} else
				{
					self::$__branch[$name] = $new_branch;
				}
			}

			if ( ! self::$__init)
			{
				global $ETHER;

				self::$config['root'] = $ETHER['ROOT'];
				self::$config['app_root'] = $ETHER['APP_ROOT'];
				self::$config['wp_root'] = $ETHER['WP_ROOT'];
				self::$config['is_plugin'] = $ETHER['IS_PLUGIN'];
				self::$config['is_theme'] = $ETHER['IS_THEME'];

				self::$config['dir'] = basename(dirname(dirname(__FILE__)));

				if (self::config('debug'))
				{
					error_reporting(E_ALL);
					set_error_handler(array('ether', 'action_error_handler'));
				}

				add_action('wp_footer', array('ether', 'action_log_footer'));
				add_action('admin_footer', array('ether', 'action_log_footer'));

				add_action('wp_head', array('ether', 'action_log_header'));
				add_action('admin_head', array('ether', 'action_log_header'));

				// now i'm prettey, pretteeeeeey sure it won't be overwritten by some punk
				add_action('init', array('ether', 'setup'), 990);
				add_action('init', array('ether', 'ready'), 999);
				add_action('after_setup_theme', array('ether', 'action_cache_begin'), 1);
				add_action('shutdown', array('ether', 'action_cache_end'));
				add_action('save_post', array('ether', 'action_cache_check'));
				add_action('comment_form_before', array('ether', 'action_enqueue_comments_reply'));

				add_filter('widget_text', 'do_shortcode');

				// formatting code was here
				add_action('wp_dashboard_setup', array('ether', 'action_dashboard_setup'));

				add_action('edit_form_advanced', array('ether', 'action_quicktags_init'));
				add_action('edit_page_form', array('ether', 'action_quicktags_init'));

				add_action('init', array('ether', 'action_wysiwig_init'));
				add_filter('tiny_mce_before_init', array('ether', 'filter_wysiwig_before_init'));
				add_filter('tiny_mce_version', array('ether', 'filter_wysiwig_tinymce_version'));

				add_action('init', array('ether', 'action_custom_post_init'), 995);
				add_action('template_redirect', array('ether', 'action_custom_post_template_redirect'));
				add_action('save_post', array('ether', 'action_custom_post_template_proccess'));
				add_action('admin_init', array('ether', 'action_custom_post_template_register'), 10);
				add_action('wp_before_admin_bar_render', array('ether', 'action_admin_bar'));

				// bug :<
				//add_action('wp_loaded', 'action_custom_post_yearly_archive_flush');
				//add_filter('rewrite_rules_array', 'filter_custom_post_yearly_archive_rule');
				add_filter('pre_get_posts', array('ether', 'filter_custom_post_search'));

				add_filter('image_send_to_editor', array('ether', 'filter_image_send_to_editor'), 10, 8);
				add_filter('media_send_to_editor', array('ether', 'filter_media_send_to_editor'), 10, 3);
				add_filter('media_upload_form_url', array('ether', 'filter_media_upload_form_url'), 10, 2);

				add_filter('pre_get_posts', array('ether', 'filter_search'));
				add_filter('admin_body_class', array('ether', 'filter_body_class'), 10, 1);

				if (self::config('cache') === TRUE)
				{
					if ( ! is_dir(WP_CONTENT_DIR.'/ether-cache'))
					{
						mkdir(WP_CONTENT_DIR.'/ether-cache', 0755);
					}

					if ( ! file_exists(WP_CONTENT_DIR.'/ether-cache/.htaccess'))
					{
						ether::write(WP_CONTENT_DIR.'/ether-cache/.htaccess', 'order allow,deny'._n.'deny from all'._n);
					}
				}

				if (self::config('is_plugin'))
				{
					if (self::config('activate_callback') != NULL)
					{
						register_activation_hook(__FILE__, self::config('activate_callback'));
					}

					if (self::config('deactivate_callback') != NULL)
					{
						register_deactivation_hook(__FILE__, self::config('deactivate_callback'));
					}
				}

				self::bind('ether.get', array('ether', 'action_wysiwig_script'));

				self::$__init = TRUE;
			}
		}

		/**
			CORE BEGIN
		**/

		/* core functions */

		public static function check_invoke()
		{
			if ( ! self::$__init OR self::$__setup)
			{
				trigger_error('ether bad invoke');
			}
		}

		public static function setup()
		{
			if ( ! self::$__setup)
			{
				// BEGIN FUNCTION

				if (self::config('debug_sql'))
				{
					define('SAVEQUERIES', true);
				}

				$uploads = wp_upload_dir();

				if ($uploads['error'] !== FALSE)
				{
					wp_die($uploads['error']);
				}

				$upload_dir = $uploads['basedir'];

				if (is_writable($uploads['basedir']))
				{
					$upload_dir .= '/'.self::config('upload_dir');

					if ( ! file_exists($upload_dir))
					{
						mkdir($upload_dir);
					}
				}

				self::config('upload_url', $uploads['baseurl'].'/'.self::config('upload_dir'));
				self::config('upload_dir', $upload_dir);

				foreach (self::$__branch as $branch => $data)
				{
					if ($data['is_theme'])
					{
						load_theme_textdomain(trim(self::config('prefix'), '_'), $data['app_root'].'locale');
					} else
					{
						load_plugin_textdomain(trim(self::config('prefix'), '_'), FALSE, dirname(dirname(plugin_basename(__FILE__))).'/locale/');
					}
				}

				add_action('login_head', array('ether', 'action_admin_header'));

				remove_action('wp_head', 'wp_generator');
				remove_action('wp_head', 'rsd_link');
				remove_action('wp_head', 'wlwmanifest_link');

				if (self::config('fix_formatting'))
				{
					remove_filter('the_content', 'wpautop');
					add_filter('the_content', 'wpautop', 999);
					add_filter('the_content', 'shortcode_unautop', 1000);

					add_filter('disable_captions', array('ether', 'filter_captions'));

					add_filter('the_content', array('ether', 'filter_formatter'), 998);
				}

				if (self::config('bootstrap_template'))
				{
					add_filter('template_include', array('ether', 'filter_bootstrap_template'));
				}

				if (self::config('hide_custom_fields'))
				{
					add_action('do_meta_boxes', array('ether', 'action_hide_custom_fields'), 10, 3);
				}

				add_action('parse_query', array('ether', 'action_custom_post_pagination_request'));

				// BEGIN FUNCTION END

				foreach (self::$modules as $module)
				{
					$module_node = explode('.', $module);
					$module_name = array_pop($module_node);

					$module_class = self::config('prefix').str_replace('-', '_', self::slug(str_replace('.', '-', $module)));

					if (class_exists($module_class))
					{
						call_user_func_array(array($module_class, 'set_class'), array($module_class));

						self::module_run(str_replace(self::config('prefix'), '', $module_class).'.__module');
						self::module_run(str_replace(self::config('prefix'), '', $module_class).'.init');
					}
				}

				if (self::config('register_default_sidebar') AND function_exists('register_sidebar'))
				{
					register_sidebar(array
					(
						'name' => self::langr('Default'),
						'id' => 'default',
						'description' => '',
						'before_widget' => '',
						'after_widget' => '',
						'before_title' => '',
						'after_title' => '',
					));
				}

				if (self::config('register_menu') AND function_exists('register_nav_menu'))
				{
					register_nav_menu('header', __('Header'));
					register_nav_menu('footer', __('Footer'));
				}

				if (is_admin())
				{
					foreach (self::$tax_metabox as $tax_type => $tax_data)
					{
						foreach ($tax_data as $tax_metabox => $tax_metabox_data)
						{
							$metabox = self::slug($tax_metabox);
							$metabox_class = str_replace('-', '_', $metabox);
							$metabox_class = self::config('prefix').'metabox_'.$metabox_class;

							if ( ! class_exists($metabox_class))
							{
								self::import('!admin.metabox.'.$metabox);
							}

							if (class_exists($metabox_class))
							{
								call_user_func_array(array($metabox_class, 'set_class'), array($metabox_class));
								call_user_func(array($metabox_class, 'init'));

								add_action($tax_type.'_edit_form', array($metabox_class, 'body'), 10, 2);
								add_action($tax_type.'_add_form_fields', array($metabox_class, 'body'), 10, 2);

								add_action('edited_'.$tax_type, array($metabox_class, 'save'), 10, 2);
								add_action('create_'.$tax_type, array($metabox_class, 'save'), 10, 2);
							}
						}
					}

					$column_callbacks = array();

					foreach (self::$columns as $column_type => $column_data)
					{
						$prefix = '';

						if ($column_type == 'post' OR $column_type == 'page')
						{
							$column_type .= 's';
						} else
						{
							$prefix = 'edit-';
						}

						add_filter('manage_'.$prefix.$column_type.'_columns', array('ether', 'filter_admin_column_create'));

						foreach ($column_data as $column_name => $column)
						{
							if ($column['callback'] !== TRUE AND ! $column['callback'] === FALSE)
							{
								if ( ! isset($column_callbacks[$column_type]))
								{
									$column_callbacks[$column_type] = array();
								}

								if ( ! in_array($column['callback'], $column_callbacks[$column_type]))
								{
									add_action('manage_'.$column_type.(($column_type == 'posts' OR $column_type == 'pages') ? '' : '_posts').'_custom_column', $column['callback']);

									$column_callbacks[$column_type][] = $column['callback'];
								}
							}
						}
					}

					if (count(self::$columns) > 0)
					{
						foreach (self::$columns as $column_type => $column_data)
						{
							$prefix = '';

							if ($column_type == 'post' OR $column_type == 'page')
							{
								$column_type .= 's';
							} else
							{
								$prefix = 'edit-';
							}

							add_action('manage_'.$column_type.(($column_type == 'posts' OR $column_type == 'pages') ? '' : '_posts').'_custom_column', array('ether', 'action_admin_column_meta'));
						}
					}

					self::action_admin_init();

					if (basename($_SERVER['SCRIPT_FILENAME']) == 'themes.php' AND self::config('is_theme'))
					{
						if (isset($_GET['activated']) AND $_GET['activated'] == 'true')
						{
							if (self::config('activate_callback') != NULL)
							{
								call_user_func(self::config('activate_callback'));
							}

							do_action('ether_activate');

							global $wp_rewrite;

							$wp_rewrite->flush_rules();
						}

						if (isset($_GET['action']) AND $_GET['action'] == 'activate' AND isset($_GET['template']) AND $_GET['template'] != self::config('dir'))
						{
							if (self::config('deactivate_callback') != NULL)
							{
								call_user_func(self::config('deactivate_callback'));
							}

							do_action('ether_deactivate');
						}
					}
				}

				do_action('ether_setup');

				if ( ! empty($_GET))
				{
					self::trigger('get', array($_GET));
				}

				if ( ! empty($_POST))
				{
					self::trigger('post', array($_POST));
				}

				if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
				{
					self::trigger('ajax', array($_GET));
				}

				self::$__setup = TRUE;
			}
		}

		public static function ready()
		{
			do_action('ether_ready');
		}

		public static function depend($types, $requirment, $error = NULL)
		{
			if ( ! is_array($types))
			{
				$types = array($types);
			}

			foreach ($types as $type)
			{
				if ( ! isset(self::$deps[$type]))
				{
					self::$deps[$type] = array();
				}

				if ($type == 'wp' OR $type == 'php')
				{
					self::$deps[$type][0] = array('requirment' => $requirment, 'error' => $error);
				} else
				{
					$found = FALSE;

					foreach (self::$deps[$type] as $dep)
					{
						if ($dep['requirment'] == $requirment)
						{
							$found = TRUE;
							break;
						}
					}

					if ( ! $found)
					{
						self::$deps[$type][] = array('requirment' => $requirment, 'error' => $error);
					}
				}
			}
		}

		public static function import($path)
		{
			$once = FALSE;

			if (substr($path, 0, 1) == '!')
			{
				$once = TRUE;
				$path = trim($path, '!');
			}

			$path = str_replace('.', trim('/', '.'), $path);

			if (count(self::$__branch) > 1)
			{
				foreach (self::$__branch as $branch => $data)
				{
					if ($data['is_theme'])
					{
						if (file_exists(STYLESHEETPATH.'/'.$path.'.'.self::config('ext')))
						{
							if ($once)
							{
								include_once(STYLESHEETPATH.'/'.$path.'.'.self::config('ext'));
								return;
							} else
							{
								include(STYLESHEETPATH.'/'.$path.'.'.self::config('ext'));
								return;
							}
						}
					}

					if (file_exists($data['app_root'].$path.'.'.self::config('ext')))
					{
						if ($once)
						{
							include_once($data['app_root'].$path.'.'.self::config('ext'));
						} else
						{
							include($data['app_root'].$path.'.'.self::config('ext'));
						}
					} else if (file_exists($data['app_root'].'ether/'.$path.'.'.self::config('ext')))
					{
						if ($once)
						{
							include_once($data['app_root'].'ether/'.$path.'.'.self::config('ext'));
						} else
						{
							include($data['app_root'].'ether/'.$path.'.'.self::config('ext'));
						}
					}
				}
			}

			if (self::config('is_theme') AND TEMPLATEPATH != STYLESHEETPATH)
			{
				if (file_exists(STYLESHEETPATH.'/'.$path.'.'.self::config('ext')))
				{
					if ($once)
					{
						include_once(STYLESHEETPATH.'/'.$path.'.'.self::config('ext'));
						return;
					} else
					{
						include(STYLESHEETPATH.'/'.$path.'.'.self::config('ext'));
						return;
					}
				}
			}

			if (file_exists(self::config('app_root').$path.'.'.self::config('ext')))
			{
				if ($once)
				{
					include_once(self::config('app_root').$path.'.'.self::config('ext'));
				} else
				{
					include(self::config('app_root').$path.'.'.self::config('ext'));
				}
			} else if (file_exists(self::config('app_root').'ether/'.$path.'.'.self::config('ext')))
			{
				if ($once)
				{
					include_once(self::config('app_root').'ether/'.$path.'.'.self::config('ext'));
				} else
				{
					include(self::config('app_root').'ether/'.$path.'.'.self::config('ext'));
				}
			}
		}

		public static function config($key, $value = NULL)
		{
			if ($value !== NULL OR (is_array($key) AND empty($value)))
			{
				if (is_array($key))
				{
					foreach($key as $k => $v)
					{
						self::config($k, $v);
					}
				} else
				{
					self::$config[$key] = $value;
				}
			} else
			{
				if (isset(self::$config[$key]))
				{
					return self::$config[$key];
				}

				return FALSE;
			}
		}

		public static function configjs($key, $value = NULL)
		{
			if ($value !== NULL OR (is_array($key) AND empty($value)))
			{
				self::$configjs[$key] = $value;
			} else
			{
				if (isset(self::$configjs[$key]))
				{
					return self::$configjs[$key];
				}

				return FALSE;
			}
		}

		public static function bind($event, $callback)
		{
			if ( ! isset(self::$bindings[$event]))
			{
				self::$bindings[$event] = array();
			}

			self::$bindings[$event][] = $callback;
		}

		public static function trigger($event, $args = array())
		{
			if (isset(self::$bindings[$event]))
			{
				foreach (self::$bindings[$event] as $callback)
				{
					call_user_func_array($callback, $args);
				}
			}
		}

		public static function script($slug = '', $path = '', $deps = array(), $ver = FALSE, $footer = FALSE)
		{
			if (is_array($slug))
			{
				foreach ($slug as $script)
				{
					$script = array_merge(array('slug' => '', 'path' => FALSE, 'deps' => array(), 'version' => FALSE, 'footer' => FALSE), $script);

					self::script($script['slug'], $script['path'], $script['deps'], $script['version'], $script['footer']);
				}
			} else
			{
				if ( ! empty($path) AND substr($path, 0, 4) != 'http')
				{
					$path = self::path($path, TRUE);
				}

				if ( ! empty($path) AND self::config('debug'))
				{
					if (strpos('?', $path) !== FALSE)
					{
						$path .= '&timestamp='.time();
					} else
					{
						$path .= '?timestamp='.time();
					}
				}

				if (empty($slug))
				{
					$slug = self::slug(str_replace('.', '-', basename($path, '.js')));
				}

				if (isset($_SERVER['HTTPS']) AND $_SERVER['HTTPS'] == 'on')
				{
					$path = str_replace('http://', 'https://', $path);
				}

				wp_enqueue_script($slug, (empty($path) ? FALSE : $path), $deps, $ver, $footer);
			}
		}

		public static function stylesheet($slug = '', $path = FALSE, $deps = array(), $ver = FALSE, $conditional = '')
		{
			if (is_array($slug))
			{
				foreach ($slug as $style)
				{
					$style = array_merge(array('slug' => '', 'path' => FALSE, 'deps' => array(), 'version' => FALSE, 'conditional' => ''), $style);

					self::stylesheet($style['slug'], $style['path'], $style['deps'], $style['version'], $style['conditional']);
				}
			} else
			{
				if ( ! empty($path) AND substr($path, 0, 4) != 'http')
				{
					$path = self::path($path, TRUE);
				}

				if ( ! empty($path) AND self::config('debug'))
				{
					if (strpos('?', $path) !== FALSE)
					{
						$path .= '&timestamp='.time();
					} else
					{
						$path .= '?timestamp='.time();
					}
				}

				if (empty($slug))
				{
					$slug = self::slug(str_replace('.', '-', basename($path, '.css')));
				}

				if (isset($_SERVER['HTTPS']) AND $_SERVER['HTTPS'] == 'on')
				{
					$path = str_replace('http://', 'https://', $path);
				}

				if ( ! empty($conditional))
				{
					global $wp_styles;

					wp_register_style($slug, (empty($path) ? FALSE : $path));
					$wp_styles->add_data($slug, 'conditional', $conditional);
				}

				wp_enqueue_style($slug, (empty($path) ? FALSE : $path), $deps, $ver);
			}
		}

		public static function module($module)
		{
			if ( ! self::$__init OR self::$__setup)
			{
				trigger_error('ether bad invoke');
			}

			if ( ! in_array($module, self::$modules))
			{
				$module_node = explode('.', $module);
				$module_name = array_pop($module_node);

				$module_class = self::config('prefix').str_replace('-', '_', self::slug(str_replace('.', '-', $module)));

				if ( ! class_exists($module_class))
				{
					self::import('modules.'.$module);
				}

				self::$modules[] = $module;
			}
		}

		public static function module_run()
		{
			$args = func_get_args();
			$name = array_shift($args);
			$node = explode('.', $name);
			$method = array_pop($node);

			if (count($node) == 1)
			{
				if (self::module_exists(implode('.', $node)))
				{
					return call_user_func_array(array(self::config('prefix').str_replace('-', '_', self::slug(str_replace('.', '-', implode('.', $node)))), $method), $args);
				}
			}

			return FALSE;
		}

		public static function module_exists($name)
		{
			return class_exists(self::config('prefix').str_replace('-', '_', self::slug(str_replace('.', '-', $name))));
		}

		public static function get_id()
		{
			global $post;

			$id = NULL;

			if (is_single())
			{
				$id = $post->ID;
			} else if (is_page() OR get_option('show_on_front') != 'posts')
			{
				if (is_home() AND get_option('show_on_front') != 'posts')
				{
					$id = get_option('page_for_posts');
				} else
				{
					if ($post)
					{
						$id = $post->ID;
					}
				}
			}

			return $id;
		}

		public static function get_posts($args = 'numberposts=1&post_type=post&text_opt=excerpt', $time_format = 'F j, Y')
		{
			global $post;
			$last_post = $post;

			$result = array();

			if ( ! is_array($args))
			{
				parse_str($args, $args);
			}

			$defaults = array('text_opt' => 'content');
			$args = array_merge($defaults, $args);
			$meta = array();

			if (isset($args['meta']))
			{
				$meta = $args['meta'];
				unset($args['meta']);
			}

			$custom_order = FALSE;

			if (isset($args['post__in']) AND ! empty($args['post__in']) AND $args['orderby'] == 'custom')
			{
				unset($args['orderby']);
				$custom_order = TRUE;
			}

			$posts = get_posts($args);
			$result = array();

			if ( ! empty($posts))
			{
				foreach ($posts as $post)
				{
					setup_postdata($post);
					array_push($result, array
					(
						'id' => get_the_ID(),
						'permalink' => get_permalink(),
						'title' => get_the_title(),
						'author' => get_the_author(),
						'date' => get_the_date(),
						'date_ymd' => get_the_date('Y-m-d'),
						'timestamp' => get_the_time('U'),
						'author_link' => get_the_author_link(),
						'content' => ($args['text_opt'] == 'excerpt' ? get_the_excerpt() : ($args['text_opt'] == 'content' ? get_the_content() : ''))
					));
				}
			}

			if ( ! empty($meta))
			{
				$result_meta = array();

				foreach ($result as $p)
				{
					$p['meta'] = array();

					foreach ($meta as $meta_key)
					{
						$p['meta'][$meta_key] = self::meta($meta_key, TRUE, $p['id']);
					}

					$result_meta[] = $p;
				}

				$result = $result_meta;
			}

			if ($custom_order)
			{
				$posts_ = array();
				$ids = array();
				$ids_ = $args['post__in'];
				$counter = 0;

				foreach ($ids_ as $k => $v)
				{
					$ids[$v] = $counter;
					$counter++;
				}

				foreach ($result as $p)
				{
					$posts_[intval($ids[$p['id']])] = $p;
				}

				$result = $posts_;

				ksort($result);
			}

			wp_reset_query();
			$post = $last_post;

			return $result;
		}

		public static function get_posts_related($args, $taxonomies = array('category', 'post_tag'), $relation = 'OR', $operator = 'LIKE')
		{
			global $post;
			$related_posts = array();

			$tax_query = array('relation' => $relation);

			if ( ! is_array($taxonomies))
			{
				$taxonomies = array($taxonomies);
			}

			foreach ($taxonomies as $tax)
			{
				$terms = wp_get_object_terms($post->ID, $tax);

				if ($terms)
				{
					$terms_ids = array();

					foreach ($terms as $term)
					{
						$terms_ids[] = $term->term_id;
					}

					$tax_query[] = array
					(
						'taxonomy' => $tax,
						'field' => 'id',
						'terms' => $terms_ids,
						'operator' => $operator
					);
				}
			}

			$args['tax_query'] = $tax_query;
			$args['post__not_in'] = array($post->ID, $post->post_parent);

			$related_posts = self::get_posts($args);

			return $related_posts;
		}

		public static function custom_get_post($slug = '', $count = 5, $post_type = 'post', $match = 'category_id', $meta_keys = array())
		{
			if (is_array($slugs))
			{
				$slug = implode(', ', $slug);
			}

			$args = array
			(
				'post_type' => $post_type,
				'orderby' => 'custom'
			);

			if ( ! empty($count))
			{
				$args['numberposts'] = $count;
			}

			if ( ! empty($slug) AND $slug != -1)
			{
				if ($match == 'category_name' OR $match == 'category_id')
				{
					$args['tax_query'] = array
					(
						'relation' => 'OR',
						array
						(
							'taxonomy' => ($post_type != 'post' ? $post_type.'_' : '').'category',
							'field' => ($match == 'category_name' ? 'slug' : 'id'),
							'terms' => self::slugify($slug, TRUE)
						)
					);
				} else if ($match == 'id')
				{
					$args['post__in'] = self::slugify($slug, TRUE);
				} else if ($match == 'name')
				{
					$args['name'] = self::slugify($slug);
				}
			}

			$args['meta'] = $meta_keys;
			$posts = self::get_posts($args);

			return $posts;
		}

		public static function find_posts($query)
		{
			$query = preg_replace('/(.*)-(html|htm|php|asp|aspx)$/', '$1', $query);
			$posts = query_posts('post_type=any&name='.$query);
			$query = str_replace('-', ' ', $query);

			if (count($posts) == 0)
			{
				$posts = query_posts('post_type=any&s='.$query);
			}

			wp_reset_query();

			return $posts;
		}

		public static function is_paged($q = NULL)
		{
			global $wp_query;

			if ($q == NULL)
			{
				$q = $wp_query;
			}

			if (self::config('pagination'))
			{
				if ( ! $q->is_single)
				{
					$posts_per_page = intval($q->get('posts_per_page'));

					$pages = intval(ceil($q->found_posts / intval($posts_per_page)));

					$is_paged = $q->get('paged');
					//return ! empty($is_paged) OR $is_paged == 0;

					return $pages > 1;
				} else
				{
					$output = '';
					ob_start();
					previous_post_link('%link', 'Prev');
					$output .= trim(ob_get_clean());
					ob_start();
					next_post_link('%link', 'Next');
					$output .= trim(ob_get_clean());

					return ! empty($output);
				}
			}

			return FALSE;
		}

		public static function pagination($list = TRUE, $prevnext = TRUE, $firstlast = FALSE)
		{
			global $wp_query;

			$page = get_query_var('paged');
			$page = ! empty($page) ? intval($page) : 1;

			$posts_per_page = intval(get_query_var('posts_per_page'));
			$pages = intval(ceil($wp_query->found_posts / $posts_per_page));
			$output = array();

			if ( ! is_single())
			{
				if ($pages > 1)
				{
					if ($firstlast)
					{
						$output['first'] = get_pagenum_link(1);
					}

					if ($prevnext AND $page > 1)
					{
						$output['prev'] = get_pagenum_link($page - 1);
					}

					if ($list)
					{
						for ($i = 1; $i <= $pages; $i++)
						{
							if ($i == $page)
							{
								$output['current'] = get_pagenum_link($i);
							}

							$output[$i] = get_pagenum_link($i);
						}
					}

					if ($prevnext AND $page < $pages)
					{
						$output['next'] = get_pagenum_link($page + 1);
					}

					if ($firstlast)
					{
						$output['last'] = get_pagenum_link($pages);
					}
				}
			} else
			{
				if ($prevnext)
				{
					ob_start();
					previous_post_link('%link');
					$link = trim(ob_get_clean());

					preg_match('/(href=")(.*?)(")/i', $link, $href);

					if (isset($href[2]))
					{
						$output['prev'] = $href[2];
					}

					ob_start();
					next_post_link('%link');
					$output = trim(ob_get_clean());

					preg_match('/(href=")(.*?)(")/i', $output, $href);

					if (isset($href[2]))
					{
						$output['next'] = $href[2];
					}
				}
			}

			return $output;
		}

		public static function get_request($key, $method = '')
		{
			$method = strtolower($method);

			switch ($method)
			{
				case 'get':
					if (isset($_GET[self::config('prefix').$key]))
					{
						return $_GET[self::config('prefix').$key];
					}
				break;

				case 'post':
					if (isset($_POST[self::config('prefix').$key]))
					{
						return $_POST[self::config('prefix').$key];
					}
				break;

				case 'file':
					if (isset($_FILES[self::config('prefix').$key]))
					{
						return $_FILES[self::config('prefix').$key];
					}
				break;

				default:
					if (isset($_REQUEST[self::config('prefix').$key]))
					{
						return $_REQUEST[self::config('prefix').$key];
					}
				break;
			}

			return FALSE;
		}

		public static function request($key, $method = '')
		{
			$method = strtolower($method);

			switch ($method)
			{
				case 'get':
					return isset($_GET[self::config('prefix').$key]) AND ! empty($_GET[self::config('prefix').$key]);
				break;

				case 'post':
					return isset($_POST[self::config('prefix').$key]) AND ! empty($_POST[self::config('prefix').$key]);
				break;

				case 'file':
					return isset($_FILES[self::config('prefix').$key]) AND ! empty($_FILES[self::config('prefix').$key]);
				break;

				default:
					return isset($_REQUEST[self::config('prefix').$key]) AND ! empty($_REQUEST[self::config('prefix').$key]);
				break;
			}
		}

		/* core actions */

		public static function action_hide_custom_fields($type, $context, $post)
		{
			foreach (array( 'normal', 'advanced', 'side' ) as $context)
			{
				$post_types = array_merge(array('post', 'page'), array_keys(self::$custom_post_types));

				foreach ($post_types as $type)
				{
					remove_meta_box('postcustom', $type, $context);
				}
			}
		}

		public static function action_enqueue_comments_reply()
		{
			if (get_option('thread_comments'))
			{
				wp_enqueue_script('comment-reply');
			}
		}

		/* core filters */

		public static function filter_formatter($content)
		{
			$content = str_replace('][', '] [', $content);
			$content = str_replace('] [/', '][/', $content);

			$new_content = '';
			$pattern_full = '{(\[raw\].*?\[/raw\])}is';
			$pattern_contents = '{\[raw\](.*?)\[/raw\]}is';
			$pieces = preg_split($pattern_full, $content, -1, PREG_SPLIT_DELIM_CAPTURE);

			foreach ($pieces as $piece)
			{
				if (preg_match($pattern_contents, $piece, $matches))
				{
					$new_content .= $matches[1];
				} else
				{
					//$piece = wpautop($piece);

					// if you have used image editor from wordpress and you have changed image align
					// wordpress put class to the img
					// we need to move class from img to a if a tag is wrapping img
					preg_match_all('/(<a.*>)?<img[^>]+>(<\/a>)?/i', $piece, $images);

					foreach ($images[0] as $image)
					{
						preg_match('/a (.*?)class="(.*?)('.self::config('image_frame_class').')[\ |"]/i', $image, $a_frame);
						preg_match('/a (.*?)class="(.*?)(align.*?)[\ |"](.*?)><img/i', $image, $a_align);
						preg_match('/img (.*?)class="(.*?)(align.*?)[\ |"]/i', $image, $img_align);

						if (isset($a_frame[3]) AND isset($img_align[3]))
						{
							if (isset($a_align[3]))
							{
								$classes = str_replace($a_align[3], $img_align[3], $a_align[0]);
								$piece = str_replace($a_align[0], $classes, $piece);
							} else
							{
								$classes = $a_frame[0].$img_align[3].(substr($a_frame[0], -1) == ' ' ? ' ' : '');
								$piece = str_replace($a_frame[0], $classes, $piece);
							}
						}
					}

					// disable image wrapping by paragraph
					preg_match_all('/<p>(<a.*>)?<img[^>]+>(<\/a>)?<\/p>/i', $piece, $images);

					if ( ! self::config('image_autop') AND count($images[0]) > 0)
					{
						foreach ($images[0] as $image)
						{
							$piece = str_replace($image, self::strip($image, '<a><img><span>'), $piece);
						}
					}

					$new_content .= $piece;
				}
			}

			// removes images from p tags
			//$new_content = preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $new_content);

			$new_content = str_replace(array('<p></p>', '<p>&nbsp;</p>', '<p></a>'), array('', '', '</a>'), $new_content);

			// replace h1 for ffs
			preg_match_all('/<h1+([^>]*?)>(.*?)<\/h1+>/si', $new_content, $matches);

			if (isset($matches[0]) AND count($matches[0]) > 0)
			{
				$headers = count($matches[0]);

				for ($i = 0; $i < $headers; $i++)
				{
					$new_content = str_replace($matches[0][$i], '<h2'.$matches[1][$i].'>'.$matches[2][$i].'</h2>', $new_content);
				}
			}

			preg_match_all('/(src=[\'"])(.*?(\.jpg|jpeg|gif|png))([\'"])/is', $new_content, $images);

			if (isset($images[2]) AND count($images[2]) > 0)
			{
				$count = count($images[2]);

				for ($i = 0; $i < $count; $i++)
				{
					$img = ether::img($images[2][$i], 'content');

					if ($img != $images[2][$i])
					{
						$new_content = str_replace($images[0][$i], 'src="'.$img.'"', $new_content);
					}
				}
			}

			return $new_content;
		}

		public static function filter_captions()
		{
			return TRUE;
		}

		public static function filter_image_editor($html, $id, $caption, $title, $align, $url, $size, $alt)
		{
			$output = '';

			preg_match('/(href=[\'"])(.*?)([\'"])/i', $html, $href);
			preg_match('/(src=[\'"])(.*?)([\'"])/i', $html, $src);
			preg_match('/(width=[\'"])(.*?)([\'"])/i', $html, $width);
			preg_match('/(height=[\'"])(.*?)([\'"])/i', $html, $height);

			$href = (isset($href[2]) ? $href[2] : NULL);
			$src = (isset($src[2]) ? $src[2] : NULL);
			$width = (isset($width[2]) ? $width[2] : NULL);
			$height = (isset($height[2]) ? $height[2] : NULL);

			if ( ! empty($href) AND ! empty($src))
			{
				$output .= '<a href="'.self::shadowbox_href($href).'" class="'.self::config('image_frame_class').' align'.$align.'">';
			}

			if ( ! empty($src))
			{
				$output .= '<img src="'.$src.'" '.(empty($href) ? 'class="'.self::config('image_frame_class').' align'.$align.'" ' : '').'alt="'.$alt.'"'.( ! empty($width) ? ' width="'.$width.'"' : '').( ! empty($height) ? ' height="'.$height.'"' : '').' title="'.$title.'" />';
			}

			if ( ! empty($href) AND ! empty($src))
			{
				$output .= '</a>';
			}

			if ( ! empty($output))
			{
				return $output;
			}

			return $html;
		}

		public static function filter_image_send_to_editor($html, $id, $caption, $title, $align, $url, $size, $alt)
		{
			$output = '';

			if (isset($_GET['ether']) AND $_GET['ether'] == 'true')
			{
				if (self::config('thumb_on_send') AND isset($_GET['width']) AND ! empty($_GET['width']) AND isset($_GET['height']) AND ! empty($_GET['height']))
				{
					$uploads = wp_upload_dir();

					preg_match('/attachment_id=(\d+)/i', $url, $attachment);

					if (count($attachment) > 0)
					{
						$url = wp_get_attachment_url($attachment[1]);
					}

					$image = $url;
					$thumbnail = $url;

					preg_match_all('/(src=[\'|"])(.*?)([\'|"])/i', $html, $src);

					if (count($src[2]) > 0)
					{
						$thumbnail = $src[2][0];
					}

					$thumbnail_path = str_replace($uploads['baseurl'], $uploads['basedir'], $thumbnail);
					$thumbnail_size = getimagesize($thumbnail_path);

					$width = explode('_', $_GET['width']);
					$height = explode('_', $_GET['height']);
					$width_count = count($width);
					$height_count = count($height);

					if ($width_count == $height_count)
					{
						$thumbnail_url = '';

						for ($i = 0; $i < $width_count; $i++)
						{
							if ($i == 0)
							{
								$thumbnail_url = self::get_image_thumbnail($image, $width[$i], $height[$i]);
							} else
							{
								self::get_image_thumbnail($image, $width[$i], $height[$i]);
							}

							if ($width[$i] == $thumbnail_size[0] AND $height[$i] == $thumbnail_size[1])
							{
								$thumbnail_url = $thumbnail;
							}
						}

						$output = $thumbnail_url;
					} else
					{
						$output = self::get_image_thumbnail($image, $width[0], $height[0]);
					}
				} else
				{
					$output = $url;
				}

				if ( ! empty($output))
				{
					if (isset($_GET['output']) AND $_GET['output'] == 'html')
					{
						return '<a href="'.self::shadowbox_href($url).'" class="'.self::config('image_frame_class').' align'.$align.'"><img src="'.$output.'" alt="'.$alt.'" title="'.$title.'" /></a>';
					} else
					{
						return $output;
					}
				}

				if (self::config('image_editor_filter') != NULL)
				{
					$html = call_user_func_array(self::config('image_editor_filter'), array($html, $id, $caption, $title, $align, $url, $size, $alt));
				}
			}

			return $html;
		}

		public static function filter_media_send_to_editor($html, $id, $attachment)
		{
			$output = '';

			if (isset($_GET['ether']) AND $_GET['ether'] == 'true' AND ( ! isset($_GET['type']) OR $_GET['type'] != 'image'))
			{
				$output = $html;

				if ( ! empty($output))
				{
					if (isset($_GET['output']) AND $_GET['output'] == 'html')
					{
						return $output;
					} else
					{
						preg_match_all('/(href=[\'|"])(.*?)([\'|"])/i', $html, $href);

						if (count($href[2]) > 0)
						{
							return $href[2][0];
						}
					}
				}
			}

			return $html;
		}

		public static function filter_media_upload_form_url($url, $type)
		{
			if (isset($_GET['ether']) AND $_GET['ether'] == 'true')
			{
				$url .= '&ether=true';

				if (isset($_GET['width']))
				{
					$url .= '&width='.$_GET['width'];
				}

				if (isset($_GET['height']))
				{
					$url .= '&height='.$_GET['height'];
				}

				if (isset($_GET['output']))
				{
					$url .= '&output='.$_GET['output'];
				}

				if (isset($_GET['single']))
				{
					$url .= '&single='.$_GET['single'];
				}

				if (isset($_GET['tab']))
				{
					$url .= '&tab='.$_GET['tab'];
				}
			}

			return $url;
		}

		public static function filter_body_class($classes)
		{
			global $wp_version;

			if (floatval(substr($wp_version, 0, 3)) < 3.8)
			{
				if (is_string($classes))
				{
					$classes .= 'admin-theme-classic';
				} else
				{
					$classes[] = 'admin-theme-classic';
				}
			} else
			{
				if (is_string($classes))
				{
					$classes .= 'admin-theme-modern';
				} else
				{
					$classes[] = 'admin-theme-modern';
				}
			}

			return $classes;
		}

		/**
			CORE END
		**/

		/**
			API BEGIN
		**/

		public static function lang()
		{
			$args = func_get_args();
			$format = array_shift($args);

			vprintf(__($format, trim(self::config('prefix'), '_')), $args);
		}

		public static function langr()
		{
			$args = func_get_args();
			$format = array_shift($args);

			return vsprintf(__($format, trim(self::config('prefix'), '_')), $args);
		}

		public static function langx($message, $context, $return = FALSE)
		{
			$output = _x($message, $context, trim(self::config('prefix'), '_'));

			if ($return)
			{
				return $output;
			}

			echo $output;
		}

		public static function langn($singular, $plural, $count, $return = FALSE)
		{
			$output = sprintf(_n($singular, $plural, $count), $count);

			if ($return)
			{
				return $output;
			}

			echo $output;
		}

		public static function header()
		{
			echo '<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="'.self::rss(TRUE).'" />';
			echo '<script type="text/javascript">if (typeof ether == \'undefined\') ether = {}; ether.path = \''.self::path('/', TRUE).'\'; ether.templatepath = \''.get_template_directory_uri().'/\', ether.stylesheetpath = \''.get_stylesheet_directory_uri().'/\';</script>';

			if (self::config('debug'))
			{
				self::script( array
				(
					array
					(
						'path' => 'ether/media/scripts/debug.js',
						'deps' => array('jquery'),
						'version' => self::config('version')
					)
				));

				self::stylesheet( array
				(
					array
					(
						'path' => 'ether/media/stylesheets/debug.css',
						'version' => self::config('version')
					)
				));
			}

			wp_head();

			if (self::option('custom_style') != '')
			{
				echo '<style type="text/css">'.stripslashes(self::option('custom_style')).'</style>';
			}
		}

		public static function footer()
		{
			wp_footer();

			$custom_script = stripslashes(self::option('custom_script'));

			preg_match_all('/(<script.*>)(.*)(<\/script>)/ism', $custom_script, $elements);

			if (isset($elements[0]) AND count($elements[0]) > 0)
			{
				$count = count($elements[0]);
				$scripts_output = array();

				for ($i = 0; $i < $count; $i++)
				{
					preg_match('/(src=[\'"])(.*?)([\'"])/i', $elements[1][$i], $src);

					if (isset($src[2]) AND ! empty($src[2]))
					{
						$scripts_output[] = '<script src="'.$src[2].'"></script>';
					} else
					{
						if ( ! empty($elements[2][$i]))
						{
							$scripts_output[] = '<script>'.$elements[2][$i].'</script>';
						}
					}

					$custom_script = str_replace($elements[0][$i], '', $custom_script);
				}

				$custom_script = trim($custom_script);

				if ( ! empty($custom_script))
				{
					$scripts_output[] = '<script>'.$custom_script.'</script>';
				}

				echo implode(_n, $scripts_output);
			} else
			{
				if ( ! empty($custom_script))
				{
					echo '<script>'.$custom_script.'</script>';
				}
			}
		}

		public static function title($separator = ' - ')
		{
			$title = '';

			if ( ! is_home() AND ! is_front_page())
			{
				$title .= wp_title('', FALSE);
			}

			$description = strip_tags(self::info('description', TRUE));

			if ( ! empty($title))
			{
				$title .= $separator.$description;
			}

			$title .= ( ! empty($title) ? $separator : '').self::info('name', TRUE);

			echo trim($title);
		}

		public static function get_url()
		{
			$url = (isset($_SERVER['HTTPS']) AND $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';

			if ($_SERVER['SERVER_PORT'] != '80')
			{
				$url .= $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
			} else
			{
				$url .= $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
			}

			return $url;
		}

		public static function url($url, $return = FALSE)
		{
			$url = self::info('url', TRUE).'/'.( ! empty($url) ? trim($url, '/').'/' : '');

			if ($return)
			{
				return $url;
			} else
			{
				echo $url;
			}
		}

		public static function path($path, $return = FALSE, $target_branch = NULL)
		{
			if (count(self::$__branch) > 1)
			{
				foreach (self::$__branch as $branch => $data)
				{
					if ($target_branch == NULL OR $branch == $target_branch)
					{
						if ($data['is_theme'] AND TEMPLATEPATH != STYLESHEETPATH AND file_exists(STYLESHEETPATH.'/'.trim($path, '/')))
						{
							if ($return)
							{
								return get_stylesheet_directory_uri().'/'.trim($path, '/');
							} else
							{
								echo get_stylesheet_directory_uri().'/'.trim($path, '/');
								return;
							}
						}

						$dir = rtrim($data['app_root'].$path, '/');

						if (is_dir($dir) OR file_exists($dir))
						{
							if ($return)
							{
								return WP_CONTENT_URL.'/'.($data['is_plugin'] ? 'plugins' : 'themes').'/'.$branch.'/'.trim($path, '/');
							} else
							{
								echo WP_CONTENT_URL.'/'.($data['is_plugin'] ? 'plugins' : 'themes').'/'.$branch.'/'.trim($path, '/');
								return;
							}
						}
					}
				}
			}

			if (self::config('is_theme') AND TEMPLATEPATH != STYLESHEETPATH AND file_exists(STYLESHEETPATH.'/'.trim($path, '/')))
			{
				if ($return)
				{
					return get_stylesheet_directory_uri().'/'.trim($path, '/');
				} else
				{
					echo get_stylesheet_directory_uri().'/'.trim($path, '/');
				}

				return;
			}

			$path = WP_CONTENT_URL.'/'.(self::config('is_plugin') ? 'plugins' : 'themes').'/'.self::config('dir').'/'.trim($path, '/');

			if ($return)
			{
				return $path;
			} else
			{
				echo $path;
			}
		}

		public static function dir($dir, $return = FALSE, $target_branch = NULL)
		{
			if (count(self::$__branch) > 1)
			{
				foreach (self::$__branch as $branch => $data)
				{
					if ($target_branch == NULL OR $branch == $target_branch)
					{
						$path = rtrim($data['app_root'].$dir, '/');

						if (is_dir($path) OR file_exists($path))
						{
							if ($return)
							{
								return $path;
							} else
							{
								echo $path;
								return;
							}
						}
					}
				}
			}

			$dir = rtrim(self::config('app_root').$dir, '/');

			if ($return)
			{
				return $dir;
			} else
			{
				echo $dir;
			}
		}

		public static function info($tag, $return = FALSE)
		{
			if (self::config('cache_data'))
			{
				$cache = self::cache('ether::info::'.$tag, NULL);

				if ($cache !== FALSE)
				{
					if ($return)
					{
						return $cache;
					} else
					{
						echo $cache;
					}

					return;
				}
			}

			$info = get_bloginfo($tag);

			if (self::config('cache_data'))
			{
				self::cache('ether::info::'.$tag, $info);
			}

			if ($return)
			{
				return $info;
			} else
			{
				echo $info;
			}
		}

		public static function option($key, $value = NULL)
		{
			if ($value === NULL)
			{
				if (self::config('cache_data'))
				{
					$cache = self::cache('ether::option::'.$key, NULL);

					if ($cache !== FALSE)
					{
						return $cache;
					}
				}

				$option = get_option(self::config('prefix').$key);

				if (self::config('cache_data'))
				{
					self::cache('ether::option::'.$key, $option);
				}

				return $option;
			} else
			{
				update_option(self::config('prefix').$key, $value);

				if (self::config('cache_data'))
				{
					self::cache('ether::option::'.$key, $value);
				}
			}

			return '';
		}

		public static function meta($key, $single = TRUE, $id = NULL, $update = FALSE)
		{
			$hidden = FALSE;

			if (substr($key, 0, 1) == '_')
			{
				$hidden = TRUE;
				$key = trim($key, '_');
			}

			if ($id == NULL AND get_option('show_on_front') != 'posts')
			{
				if (is_home() AND get_option('show_on_front') != 'posts')
				{
					$id = get_option('page_for_posts');
				}
			}

			if ( ! $update)
			{
				if (self::config('cache_data'))
				{
					$cache = self::cache('ether::meta::'.$key.$id, NULL);

					if ($cache !== FALSE)
					{
						return $cache;
					}
				}

				global $post;

				if ($id != NULL OR $post)
				{
					$fields = get_post_meta($id != NULL ? $id : $post->ID, ($hidden ? '_' : '').self::config('prefix').$key, $single);
					//$fields = get_post_meta(($id != NULL ? $id : $post->ID), self::config('prefix').$key, $single);

					if (self::config('cache_data'))
					{
						self::cache('ether::meta'.$key.$id, $fields);
					}

					return $fields;
				}
			} else
			{
				global $post;

				update_post_meta($id != NULL ? $id : $post->ID, ($hidden ? '_' : '').self::config('prefix').$key, $single);
				//add_post_meta($id != NULL ? $id : $post->ID, self::config('prefix').$key, )

				if (self::config('cache_data'))
				{
					//self::cache('ether::meta'.$key, $single);
				}
			}

			return NULL;
		}

		public static function snippet($filename, $data = array())
		{
			$trace = debug_backtrace();
			$file = explode(self::config('dir').'/admin', $trace[0]['file']);
			$file = '/'.str_replace('.php', '', trim($file[1], '/')).'/'.$filename.'.html';

			if (file_exists(self::dir('ether/admin/snippets/', TRUE).$file))
			{
				$snippet = self::read(self::dir('ether/admin/snippets/', TRUE).$file);
			} else if (file_exists(self::dir('admin/snippets/', TRUE).$file))
			{
				$snippet = self::read(self::dir('admin/snippets/', TRUE).$file);
			}

			foreach ($data as $k => $v)
			{
				$snippet = str_replace('{$'.$k.'}', $v);
			}

			return $snippet;
		}

		public static function feedburner_count($username = NULL, $separator = '.')
		{
			if ($username == NULL)
			{
				$username = self::option('social_feedburner');
			}

			$count = '0';

			if ($username != '')
			{
				$cache = self::cache('feedburner_subscribers', NULL);

				if ($cache !== FALSE)
				{
					return $cache;
				}

				$feedburner = self::http_get('http://feedburner.google.com/api/awareness/1.0/GetFeedData?uri='.$username);
				$begin = 'circulation="';
				$end = '"';
				$parts = explode($begin, $feedburner);
				$page = $parts[1];
				$parts = explode($end, $page);
				$count = $parts[0];

				if ($count == '')
				{
					$count = '0';
				}
			}

			if (strlen($count) > 3)
			{
				$length = strlen($count);
				$count = substr($count, 0, $length - 3).$separator.substr($count, $length - 3, 3);
			}

			self::cache('feedburner_subscribers', $count);

			return $count;
		}

		public static function youtube_channel($username = NULL)
		{
			if ($username == NULL)
			{
				$username = self::option('social_youtube');
			}

			$code = array();

			if ($username != '')
			{
				$cache = self::cache('youtube_channel', NULL);

				if ($cache !== FALSE)
				{
					return $cache;
				}

				$channel = self::http_get('http://gdata.youtube.com/feeds/api/users/'.$username.'/uploads?max-results=1&v=2&alt=json');
				$channel = json_decode($channel);

				$code[0]['url'] = $channel->feed->entry[0]->link[0]->href;
				$title = (array)$channel->feed->entry[0]->title;
				$code[0]['title'] = $title['$t'];
			}

			self::cache('youtube_channel', $code);

			return $code;
		}

		public static function twitter_count($username = NULL, $separator = '')
		{
			if ($username == NULL)
			{
				$username = self::option('social_twitter');
			}

			$count = '0';

			if ($username != '')
			{

				$cache = self::cache('twitter_followers'.$username, NULL);

				if ($cache !== FALSE)
				{
					return $cache;
				}

				$twitter = self::http_get('http://twitter.com/users/show/'.$username.'.xml');
				$begin = '<followers_count>';
				$end = '</followers_count>';
				$parts = explode($begin, $twitter);
				$page = $parts[1];
				$parts = explode($end, $page);
				$count = $parts[0];

				if ($count == '')
				{
					$count = '0';
				}
			}

			if (strlen($count) > 3)
			{
				$length = strlen($count);
				$count = substr($count, 0, $length - 3).$separator.substr($count, $length - 3, 3);
			}

			self::cache('twitter_followers'.$username, $count);

			return $count;
		}

		public static function twitter_time($time)
		{
			$delta = time() - $time;

			if ($delta < 60)
			{
				return self::langr('less than a minute ago');
			} else if ($delta < 120)
			{
				return self::langr('about a minute ago');
			} else if ($delta < (45 * 60))
			{
				return self::langr('%s minutes ago', floor($delta / 60));
			} else if ($delta < (90 * 60))
			{
				return self::langr('about an hour ago');
			} else if ($delta < (24 * 60 * 60))
			{
				return self::langr('about %s hours ago', floor($delta / 3600));
			} else if ($delta < (48 * 60 * 60))
			{
				return self::langr('1 day ago');
			} else
			{
				return self::langr('%s days ago', floor($delta / 86400));
			}
		}

		public static function flickr_feed($id = NULL, $count = 1, $tags = '')
		{
			$feed = array();

			if ($id == NULL)
			{
				$id = self::option('social_flickr');
			}

			if ($id != '')
			{
				$cache = self::cache('flickr_feed'.$id.$count.$tags, NULL);

				if ($cache !== FALSE)
				{
					return $cache;
				}

				$flickr = self::http_get('http://api.flickr.com/services/feeds/photos_public.gne?id='.$id.'&format=php_serial'.( ! empty($tags) ? '&tags='.$tags : ''));
				$flickr = unserialize($flickr);

				if ($count <= 0)
				{
					$count = 20;
				}

				$count = min($count, count($flickr['items']));

				if ($flickr !== FALSE)
				{
					for ($i = 0; $i < $count; $i++)
					{
						$feed[] = array
						(
							'title' => $flickr['items'][$i]['title'],
							'link' => $flickr['items'][$i]['url'],
							'image' => $flickr['items'][$i]['photo_url'],
							'thumbnail' => $flickr['items'][$i]['t_url']
						);
					}
				}
			}

			self::cache('flickr_feed'.$id.$count.$tags, $feed);

			return $feed;
		}

		public static function google_map($location = NULL, $width = NULL, $height = NULL, $zoom = 14, $view = 0, $hide_bubble = TRUE, $return = FALSE)
		{
			if ($location == NULL)
			{
				$location = self::option('social_location');
			}

			if ($view < 0 OR $view > 2)
			{
				$view = 0;
			}

			// map, sattelite, map + terrain
			$views = array('m', 'k', 'p');

			$location = htmlspecialchars($location);
			//$map = '<iframe'.($width != NULL ? ' width="'.$width.'"' : '').($height != NULL ? ' height="'.$height.'"' : '').' frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?q='.urlencode($location).'&t='.$views[$view].'&z='.$zoom.($hide_bubble ? '&iwloc=' : '').'&output=embed"></iframe>';
			$map = '<iframe'.($width != NULL ? ' width="'.$width.'"' : '').($height != NULL ? ' height="'.$height.'"' : '').' frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?q='.urlencode($location).'&t='.$views[$view].'&output=embed"></iframe>';

			if ($return)
			{
				return $map;
			}

			echo $map;
		}

		public static function twitter_feed($username = NULL, $count = 1)
		{
			if ($username == NULL)
			{
				$username = self::option('social_twitter');
			}

			$tweets = array();

			if ($username != '')
			{
				$cache = self::cache('twitter_tweets'.$username.$count, NULL);

				if ($cache !== FALSE AND is_array($cache) AND count($cache) > 0)
				{
					return $cache;
				}

				$twitter = self::http_get('http://api.twitter.com/1/statuses/user_timeline.json?include_entities=true&screen_name='.$username.'&count='.$count);

				$twitter = json_decode($twitter);

				if ( ! isset($twitter->error) OR ! $twitter->error)
				{
					foreach ($twitter as $tweet)
					{
						$tweet->text = strip_tags($tweet->text);

						$tweet->text = ' '.preg_replace( "/(([[:alnum:]]+:\/\/)|www\.)([^[:space:]]*)([[:alnum:]#?\/&=])/i", "<a href=\"\\1\\3\\4\" target=\"_blank\">\\1\\3\\4</a>", $tweet->text);
						$tweet->text = preg_replace( "/(([a-z0-9_]|\\-|\\.)+@([^[:space:]]*)([[:alnum:]-]))/i", "<a href=\"mailto:\\1\">\\1</a>", $tweet->text);
						$tweet->text = preg_replace( "/ +@([a-z0-9_]*) ?/i", " <a href=\"http://twitter.com/\\1\">@\\1</a> ", $tweet->text);
						$tweet->text = preg_replace( "/ +#([a-z0-9_]*) ?/i", " <a href=\"http://twitter.com/search?q=%23\\1\">#\\1</a> ", $tweet->text);
						$tweet->text = preg_replace("/>(([[:alnum:]]+:\/\/)|www\.)([^[:space:]]{30,40})([^[:space:]]*)([^[:space:]]{10,20})([[:alnum:]#?\/&=])</", ">\\3...\\5\\6<", $tweet->text);
						$tweet->text = trim($tweet->text);

						if ($tweet->text != '')
						{
							array_push($tweets, array
							(
								'tweet' => $tweet->text,
								'time' => self::twitter_time(strtotime(str_replace('+0000', '', $tweet->created_at))),
								'link' => 'http://twitter.com/'.$username.'/statuses/'.$tweet->id
							));
						}
					}
				}
			}

			self::cache('twitter_tweets'.$username.$count, $tweets);

			return $tweets;
		}

		public static function social_profile($service, $username)
		{
			$url = '';

			if (preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $username))
			{
				return $username;
			}

			if ( ! empty($username))
			{
				if ($service == 'mail' OR $service == 'email')
				{
					$url = 'mailto:'.$username;
				} else if ($service == 'envato')
				{
					$url = 'http://themeforest.com/user/'.$username;
				} else if ($service == 'reddit')
				{
					$url = 'http://reddit.com/user/'.$username;
				} else if ($service == 'delicious')
				{
					$url = 'http://delicious.com/'.$username;
				} else if ($service == 'linkedin')
				{
					if (is_numeric($username))
					{
						$url = 'http://www.linkedin.com/profile/view?id='.$username;
					} else
					{
						$url = 'http://www.linkedin.com/'.$username;
					}
				} else if ($service == 'myspace')
				{
					$url = 'http://www.myspace.com/'.$username;
				} else if ($service == 'quora')
				{
					$url = 'http://www.quora.com/'.$username;
				} else if ($service == 'sharethis')
				{
					$url = 'http://sharethis.com/'.$username;
				} else if ($service == 'skype')
				{
					$url = 'callto://'.$username;
				} else if ($service == 'stumbleupon')
				{
					$url = 'http://www.stumbleupon.com/stumbler/'.$username;
				} else if ($service == 'tumblr')
				{
					$url = 'http://'.$username.'.tumblr.com/';
				} else if ($service == 'digg')
				{
					$url = 'http://digg.com/'.$username;
				} else if ($service == 'rss')
				{
					$url = 'http://feeds.feedburner.com/'.$username;
				} else if ($service == 'facebook')
				{
					if (is_numeric($username))
					{
						$url = 'http://facebook.com/profile.php?id='.$username;
					} else
					{
						$url = 'http://facebook.com/'.$username;
					}
				} else if ($service == 'twitter')
				{
					$url = 'http://twitter.com/'.$username;
				} else if ($service == 'dribble' OR $service == 'dribbble')
				{
					$url = 'http://dribbble.com/'.$username;
				} else if ($service == 'flickr')
				{
					$url = 'http://www.flickr.com/people/'.$username.'/';
				} else if ($service == 'youtube')
				{
					$url = 'http://youtube.com/'.$username;
				} else if ($service == 'vimeo')
				{
					$url = 'http://vimeo.com/'.$username;
				} else if ($service == 'googleplus' OR $service == 'google+')
				{
					$url = 'http://plus.google.com/'.$username;
				}
			} else
			{
				return '';
			}

			return $url;
		}

		public static function share_link($service, $title = NULL, $url = NULL, $return = FALSE)
		{
			$share_title = urlencode(trim(($title != NULL ? $title : wp_title('', FALSE, ''))));
			$share_url = urlencode(trim(($url != NULL ? $url : ('http'.(isset($_SERVER['HTTPS'] )? 's' : '').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']))));

			switch ($service)
			{
				case 'facebook':
					$url = 'http://www.facebook.com/share.php?u='.$share_url.'&t='.$share_title;
				break;

				case 'google+':
					$url = 'https://m.google.com/app/plus/x/?v=compose&content='.$share_title.' - '.$share_url;
				break;

				case 'twitter':
					$url = 'http://twitter.com/home?status='.$share_title.'%20'.$share_url;
				break;

				case 'digg':
					$url = 'http://digg.com/submit?phase=2&url='.$share_url.'&title='.$share_title;
				break;

				case 'reddit':
					$url = 'http://reddit.com/submit?url='.$share_url.'&title='.$share_title;
				break;

				case 'stumbleupon':
					$url = 'http://www.stumbleupon.com/submit?url='.$share_url.'&title='.$share_title;
				break;

				case 'delicious':
					$url = 'http://del.icio.us/post?url='.$share_url.'&title='.$share_title;
				break;

				case 'addthis':
					$url = 'http://www.addthis.com/bookmark.php';
				break;
			}

			if ($return)
			{
				return $url;
			}

			echo $url;
		}

		public static function rss($return = FALSE)
		{
			$feedburner = self::option('social_feedburner');
			$url = 'http://feeds.feedburner.com/';

			if ($feedburner != '')
			{
				$url .= $feedburner;
			} else
			{
				$url = self::info('rss2_url', TRUE);
			}

			if ($return)
			{
				return $url;
			}

			echo $url;
		}

		public static function video($url, $width = NULL, $height = NULL, $class = '')
		{
			if (substr($url, 0, 4) != 'http')
			{
				$url = 'http://'.$url;
			}

			$url = trim($url);
			$video_url = '';

			$url_data = parse_url($url);

			if (isset($url_data['host']))
			{
				$url_data['host'] = str_replace('www.', '', $url_data['host']);
			}

			if (isset($url_data['host']))
			{
				if ($url_data['host'] == 'video.google.com')
				{
					$params = explode('?', $url);
					parse_str(html_entity_decode($params[1]), $params);

					foreach ($params as $k => $v)
					{
						if (strtolower($k) == 'docid' OR $url == 'http://video.google.com/googleplayer.swf?docid='.$v)
						{
							$video_url = 'http://video.google.com/googleplayer.swf?docid='.$v;

							break;
						}
					}
				} else if ($url_data['host'] == 'youtube.com')
				{
					$params = explode('?', $url);
					parse_str(html_entity_decode($params[1]), $params);

					foreach ($params as $k => $v)
					{
						if (strtolower($k) == 'v')
						{
							$video_url = 'http://www.youtube.com/embed/'.$v;

							break;
						}
					}

					if (count($params) == 0 AND strpos($url, '/embed/') !== FALSE)
					{
						$video_url = $url;
					}
				} else if ($url_data['host'] == 'ted.com')
				{
					preg_match('/talks\/([\d\w_\.]*)/', $url, $id);

					if ( ! empty($id[1]))
					{
						$video_url = 'http://embed.ted.com/talks/'.$id[1];
					}
				} else if ($url_data['host'] == 'vimeo.com' OR $url_data['host'] == 'player.vimeo.com')
				{
					preg_match('/(\d+)/', $url, $id);

					if ( ! empty($id[1]))
					{
						$video_url = 'http://player.vimeo.com/video/'.$id[1];
					}
				} else if ($url_data['host'] == 'blip.tv')
				{
					preg_match('/file\/(\d+)\//', $url, $id);

					if ( ! empty($id[1]))
					{
						$video_url = 'http://blip.tv/play/'.$id[1];
					}
				}
			}

			if (empty($video_url))
			{
				return '';
			}

			return '<iframe src="'.$video_url.'"'.( ! empty($class) ? ' class="'.$class.'"' : '').' width="'.( ! empty($width) ? $width : '480').'" height="'.( ! empty($height) ? $height : '290').'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
		}

		public static function shadowbox_href($url)
		{
			$url = trim($url);

			preg_match('@^(?:http://)?(?:www.)?([^/]+)@i', $url, $matches);

			if ($matches[1] == 'video.google.com')
			{
				$params = explode('?', $url);
				parse_str(html_entity_decode($params[1]), $params);

				foreach ($params as $k => $v)
				{
					if (strtolower($k) == 'docid')
					{
						$url = 'http://video.google.com/googleplayer.swf?docid='.$v;

						return $url.'" rel="shadowbox;width=480;height=290;player=iframe';
					}
				}
			} else if ($matches[1] == 'youtube.com')
			{
				$params = explode('?', $url);
				parse_str(html_entity_decode($params[1]), $params);

				foreach ($params as $k => $v)
				{
					if (strtolower($k) == 'v')
					{
						$url = 'http://www.youtube.com/embed/'.$v;

						return $url.'" rel="shadowbox;width=480;height=290;player=iframe';
					}
				}
			} else if ($matches[1] == 'vimeo.com')
			{
				preg_match('/(\d+)/', $url, $id);

				if ( ! empty($id[1]))
				{
					$url = 'http://player.vimeo.com/video/'.$id[1];

					return $url.'" rel="shadowbox;width=480;height=290;player=iframe';
				}
			} else if ($matches[1] == 'blip.tv')
			{
				preg_match('/file\/(\d+)\//', $url, $id);

				if ( ! empty($id[1]))
				{
					$url = 'http://blip.tv/play/'.$id[1];

					return $url.'" rel="shadowbox;width=480;height=290;player=iframe';
				}
			}

			preg_match('/(?i)\.(jpg|png|gif)$/', $url, $ext);

			if ( ! empty($ext))
			{
				return $url.'" rel="shadowbox';
			}

			return $url;
		}

		public static function avatar($id_or_email, $size, $default, $return = FALSE)
		{
			if ($id_or_email === NULL)
			{
				global $post;

				$id_or_email = get_the_author_meta('user_email');
			}

			$gravatar = get_avatar($id_or_email, $size, $default);

			preg_match('/(src=[\'"])(.*?)([\'"])/i', $gravatar, $src);

			if ($return)
			{
				return $src[2];
			}

			echo $src[2];
		}

		public static function breadcrumb($container = 'ul', $element = 'li', $before = '', $after = '')
		{
			global $post;
			global $wp_query;

			$output = '';
			$depth = 0;
			$subdepth = 0;

			$output .= _t(5).'<'.$element.'>'._n;
			$output .= _t(5).'	<a href="'.home_url().'">'.$before.self::info('name', TRUE).$after.'</a>'._n;

			if (is_category())
			{
				$cat = $wp_query->get_queried_object();
				$current_cat = get_category($cat->term_id);

				if ($current_cat->parent != 0)
				{
					$parent_cat = get_category($current_cat->parent);
					$depth++;
					$output .= _t(5 + $depth).'<'.$container.' class="sub-'.$depth.'">'._n;
					$output .= _t(5 + $depth).'	<'.$element.'>'._n;
					$output .= _t(5 + $depth).'		<a href="'.get_category_link($parent_cat).'">'.$before.$parent_cat->cat_name.$after.'</a>'._n;
					$subdepth++;
				}

				$depth++;
				$output .= _t(5 + $depth + $subdepth).'<'.$container.' class="sub-'.$depth.'">'._n;
				$output .= _t(5 + $depth + $subdepth).'	<'.$element.'>'._n;
				$output .= _t(5 + $depth + $subdepth).'		<a href="'.get_category_link($current_cat).'">'.$before.$current_cat->cat_name.$after.'</a>'._n;
			} else if (is_day())
			{
				$depth++;
				$output .= _t(5 + $depth).'<'.$container.' class="sub-'.$depth.'">'._n;
				$output .= _t(5 + $depth).'	<'.$element.'>'._n;
				$output .= _t(5 + $depth).'		<a href="'.get_year_link(get_the_time('Y')).'">'.$before.get_the_time('Y').$after.'</a>'._n;
				$depth++;
				$subdepth++;
				$output .= _t(5 + $depth + $subdepth).'<'.$container.' class="sub-'.$depth.'">'._n;
				$output .= _t(5 + $depth + $subdepth).'	<'.$element.'>'._n;
				$output .= _t(5 + $depth + $subdepth).'		<a href="'.get_month_link(get_the_time('Y'), get_the_time('m')).'">'.$before.get_the_time('F').$after.'</a>'._n;
				$depth++;
				$subdepth++;
				$output .= _t(5 + $depth + $subdepth).'<'.$container.' class="sub-'.$depth.'">'._n;
				$output .= _t(5 + $depth + $subdepth).'	<'.$element.'>'._n;
				$output .= _t(5 + $depth + $subdepth).'		<a href="'.self::get_url().'">'.$before.get_the_time('d').$after.'</a>'._n;
			} else if (is_month())
			{
				$depth++;
				$output .= _t(5 + $depth).'<'.$container.' class="sub-'.$depth.'">'._n;
				$output .= _t(5 + $depth).'	<'.$element.'>'._n;
				$output .= _t(5 + $depth).'		<a href="'.get_year_link(get_the_time('Y')).'">'.$before.get_the_time('Y').$after.'</a>'._n;
				$depth++;
				$subdepth++;
				$output .= _t(5 + $depth + $subdepth).'<'.$container.' class="sub-'.$depth.'">'._n;
				$output .= _t(5 + $depth + $subdepth).'	<div>'._n;
				$output .= _t(5 + $depth + $subdepth).'		<a href="'.self::get_url().'">'.$before.get_the_time('F').$after.'</a>'._n;
			} else if (is_year())
			{
				$depth++;
				$output .= _t(5 + $depth).'<'.$container.' class="sub-'.$depth.'">'._n;
				$output .= _t(5 + $depth).'	<'.$element.'>'._n;
				$output .= _t(5 + $depth).'		<a href="'.self::get_url().'">'.$before.get_the_time('Y').$after.'</a>'._n;
			} else if (is_single() AND ! is_attachment())
			{
				if (get_post_type() == 'post')
				{
					$cat = get_the_category();


					if ( ! empty($cat))
					{
						$cat = $cat[0];

						if ($cat->parent != 0)
						{
							$parent_cat = get_category($cat->parent);
							$depth++;
							$output .= _t(5 + $depth).'<'.$container.' class="sub-'.$depth.'">'._n;
							$output .= _t(5 + $depth).'	<'.$element.'>'._n;
							$output .= _t(5 + $depth).'		<a href="'.get_category_link($parent_cat).'">'.$before.$parent_cat->cat_name.$after.'</a>'._n;
							$subdepth++;
						}

						$depth++;
						$output .= _t(5 + $depth + $subdepth).'<'.$container.' class="sub-'.$depth.'">'._n;
						$output .= _t(5 + $depth + $subdepth).'	<'.$element.'>'._n;
						$output .= _t(5 + $depth + $subdepth).'		<a href="'.get_category_link($cat).'">'.$before.$cat->cat_name.$after.'</a>'._n;
						$subdepth++;
					}
				} else
				{
					$type = get_post_type_object(get_post_type($post->post_parent != 0 ? $post->post_parent : $post->ID));

					if ( ! empty($type))
					{
						$depth++;
						$output .= _t(5 + $depth).'<'.$container.' class="sub-'.$depth.'">'._n;
						$output .= _t(5 + $depth).'	<'.$element.'>'._n;
						$output .= _t(5 + $depth).'		<a href="'.get_post_type_archive_link($type).'">'.$before.$type->labels->singular_name.$after.'</a>'._n;
						$subdepth++;
					}

					if ($post->post_parent != 0)
					{
						$ancestors = array_reverse(get_post_ancestors($post->ID));

						foreach ($ancestors as $ancestor)
						{
							$depth++;
							$output .= _t(5 + $depth + $subdepth).'<'.$container.' class="sub-'.$depth.'">'._n;
							$output .= _t(5 + $depth + $subdepth).'	<'.$element.'>'._n;
							$output .= _t(5 + $depth + $subdepth).'		<a href="'.get_permalink(is_object($ancestor) ? $ancestor->ID : $ancestor).'">'.$before.get_the_title(is_object($ancestor) ? $ancestor->ID : $ancestor).$after.'</a>'._n;
							$subdepth++;
						}
	        		}
				}

				$depth++;
				$output .= _t(5 + $depth + $subdepth).'<'.$container.' class="sub-'.$depth.'">'._n;
				$output .= _t(5 + $depth + $subdepth).'	<'.$element.'>'._n;
				$output .= _t(5 + $depth + $subdepth).'		<a href="'.self::get_url().'">'.$before.get_the_title().$after.'</a>'._n;
			} else if (is_attachment())
			{
				$parent = get_post($post->post_parent);
				$cat = get_the_category($parent->ID);

				if ( ! empty($cat))
				{
					$cat = $cat[0];

					if ($cat->parent != 0)
					{
						$parent_cat = get_category($cat->parent);
						$depth++;
						$output .= _t(5 + $depth).'<'.$container.' class="sub-'.$depth.'">'._n;
						$output .= _t(5 + $depth).'	<'.$element.'>'._n;
						$output .= _t(5 + $depth).'		<a href="'.get_category_link($parent_cat).'">'.$before.$parent_cat->cat_name.$after.'</a>'._n;
						$subdepth++;
					}

					$depth++;
					$output .= _t(5 + $depth + $subdepth).'<'.$container.' class="sub-'.$depth.'">'._n;
					$output .= _t(5 + $depth + $subdepth).'	<'.$element.'>'._n;
					$output .= _t(5 + $depth + $subdepth).'		<a href="'.get_category_link($cat).'">'.$before.$cat->cat_name.$after.'</a>'._n;
					$subdepth++;
				}

				$depth++;
				$output .= _t(5 + $depth + $subdepth).'<'.$container.' class="sub-'.$depth.'">'._n;
				$output .= _t(5 + $depth + $subdepth).'	<'.$element.'>'._n;
				$output .= _t(5 + $depth + $subdepth).'		<a href="'.self::get_url().'">'.$before.get_the_title().$after.'</a>'._n;
			} else if (is_page() OR (is_home() AND get_option('show_on_front') != 'posts'))
			{
				if ($post->post_parent != 0)
				{
					$ancestors = array_reverse(get_post_ancestors($post->ID));

					foreach ($ancestors as $ancestor)
					{
						$depth++;
						$output .= _t(5 + $depth + $subdepth).'<'.$container.' class="sub-'.$depth.'">'._n;
						$output .= _t(5 + $depth + $subdepth).'	<'.$element.'>'._n;
						$output .= _t(5 + $depth + $subdepth).'		<a href="'.get_permalink(is_object($ancestor) ? $ancestor->ID : $ancestor).'">'.$before.get_the_title(is_object($ancestor) ? $ancestor->ID : $ancestor).$after.'</a>'._n;
						$subdepth++;
					}
        		}

				$depth++;
				$output .= _t(5 + $depth + $subdepth).'<'.$container.' class="sub-'.$depth.'">'._n;
				$output .= _t(5 + $depth + $subdepth).'	<'.$element.'>'._n;
				$output .= _t(5 + $depth + $subdepth).'		<a href="'.self::get_url().'">'.$before.get_the_title(ether::get_id()).$after.'</a>'._n;
			} else if (is_search())
			{
				$depth++;
				$output .= _t(5 + $depth).'<'.$container.' class="sub-'.$depth.'">'._n;
				$output .= _t(5 + $depth).'	<'.$element.'>'._n;
				$output .= _t(5 + $depth).'		<a href="'.self::get_url().'">'.$before.ether::langr('Search results for "%s"', get_search_query()).$after.'</a>'._n;
			} else if (is_tag())
			{
				$tag = $wp_query->get_queried_object();
				$tag = get_tag($tag->term_id);

				$depth++;
				$output .= _t(5 + $depth).'<'.$container.' class="sub-'.$depth.'">'._n;
				$output .= _t(5 + $depth).'	<'.$element.'>'._n;
				$output .= _t(5 + $depth).'		<a href="'.self::get_url().'">'.$before.ether::langr('Tag "%s"', $tag->name).$after.'</a>'._n;
			} else if (is_author())
			{
				global $author;
				$userdata = get_userdata($author);

				$depth++;
				$output .= _t(5 + $depth).'<'.$container.' class="sub-'.$depth.'">'._n;
				$output .= _t(5 + $depth).'	<'.$element.'>'._n;
				$output .= _t(5 + $depth).'		<a href="'.self::get_url().'">'.$before.ether::langr('Author "%s"', $userdata->display_name).$after.'</a>'._n;
			} else if (is_404())
			{
				$depth++;
				$output .= _t(5 + $depth).'<'.$container.' class="sub-'.$depth.'">'._n;
				$output .= _t(5 + $depth).'	<'.$element.'>'._n;
				$output .= _t(5 + $depth).'		<a href="'.self::get_url().'">'.$before.ether::langr('Error 404').$after.'</a>'._n;
			}

			if (get_query_var('paged'))
			{
				if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author())
				{
					$last_after = strrpos($output, $after.'</a>');

					if (empty($after) OR $last_after === FALSE)
					{
						$output .= ' ('.ether::langr('Page %s', get_query_var('paged')).')'.$after.'</a>';
					} else
					{
						$output = substr_replace($output, ' ('.ether::langr('Page %s', get_query_var('paged')).')', $last_after, $after.'</a>');
					}
				}
			}

			$count = $depth;

			for ($i = 0; $i < $count; $i++)
			{
				$output .= _t(5 + $depth + $subdepth).'	</'.$element.'>'._n;
				$output .= _t(5 + $depth + $subdepth).'</'.$container.'>'._n;
				$depth--;
				$subdepth--;
			}

			$output .= _t(5 + $depth).'</'.$element.'>'._n;

			echo $output;
		}

		public static function navigation($args = 'sort_column=menu_order', $menu = 'header', $show_home = TRUE, $return = FALSE)
		{
			if ( ! is_array($args))
			{
				parse_str($args, $args);
			}

			$defaults = array
			(
				'depth' => 0,
				'sort_column' => 'menu_order',
				'fallback_cb' => NULL
			);

			$args = array_merge($defaults, $args);

			$nav = '';

			if (function_exists('wp_nav_menu'))
			{
				ob_start();
				wp_nav_menu('fallback_cb='.$args['fallback_cb'].'&sort_column='.$args['sort_column'].'&container=&depth='.$args['depth'].'&menu='.$menu.'&theme_location='.$menu.'&menu_id=&menu_class='.$args['class'].'&walker='.$args['walker']);
				$wp_nav = trim(ob_get_clean());

				preg_match('/^(<ul.*>)(.*)(<\/ul>)\z/ismU', $wp_nav, $elements);

				if (isset($elements[2]))
				{
					$nav .= str_replace('current-menu-item', 'current', $elements[2]);
				}
			} else
			{
				$pages = get_pages('depth='.$args['depth'].'&meta_key=menu&meta_value=exclude');
				$page_exclude = array();

				foreach ($pages as $page)
				{
					$page_exclude[] = $page->ID;
				}

				$nav .= str_replace('current_page_item', 'current', wp_list_pages('title_li=&sort_column='.$args['sort_column'].'&depth='.$args['depth'].'&echo=0&'.($menu == 'header' ? '' : 'meta_key=menu&meta_value='.$menu.'&').'exclude='.implode(',', $page_exclude).'&exclude_tree='.implode(',', $page_exclude)));
			}

			if ( ! empty($nav))
			{
				if ( ! function_exists('wp_nav_menu') AND empty($nav))
				{
					$nav = call_user_func($args['fallback_cb']);
				}

				$last_occurrance = strripos($nav, 'class="');
				$last_before = substr($nav, 0, ($last_occurrance + 7));
				$last_after = substr($nav, ($last_occurrance + 7), strlen($nav));

				$nav = $last_before.'last-child '.$last_after;
			}

			if (empty($nav) OR $show_home)
			{
				$nav = '<li'.(empty($nav) ? ' class="' : '').((is_home() OR is_front_page()) ? ( ! empty($nav) ? ' class="' : '').'current'.( ! empty($nav) ? '"' : ' ') : '').(empty($nav) ? 'last-child"' : '').'><a href="'.self::info('home', TRUE).'">'.self::langx('Home', 'navigation', TRUE).'</a></li>'.$nav;
			}

			$nav = str_replace('current-page-ancestor', 'current', $nav);

			if ($return)
			{
				return $nav;
			}

			echo $nav;
		}

		public static function img($img, $source_id = '')
		{
			$img = apply_filters('ether_img', $img, $source_id);

			return $img;
		}

		/**
			API END
		**/

		/**
			CACHE BEGIN
		**/

		/* cache functions */

		public static function cache($key, $content = NULL, $time = NULL)
		{
			if ($time === NULL)
			{
				$time = self::config('cache_lifetime');
			}

			$path = WP_CONTENT_DIR.'/ether-cache/';

			if ( ! is_dir($path) OR ! is_writable($path))
			{
				return FALSE;
			}

			$file = $path.md5($key);

			if ($content != NULL)
			{
				file_put_contents($file, serialize($content));
			} else
			{
				if (file_exists($file) AND is_file($file))
				{
					if ((filemtime($file) + $time) > time())
					{
						return unserialize(file_get_contents($file));
					}
				}

				return FALSE;
			}

			return TRUE;
		}

		public static function cache_delete($key)
		{
			$path = WP_CONTENT_DIR.'/ether-cache/';

			if ( ! is_dir($path) OR ! is_writable($path))
			{
				return FALSE;
			}

			$file = $path.md5($key);

			if ( ! file_exists($file))
			{
				return FALSE;
			}

			return unlink($file);
		}

		public static function cache_clean($time = NULL)
		{
			$path = WP_CONTENT_DIR.'/ether-cache/';
			$files = array();

			if (is_dir($path))
			{
				$scan_results = array_diff(scandir($path), array('.', '..', '.htaccess'));

				foreach($scan_results as $result)
				{
					if (file_exists($path.$result) AND is_file($path.$result))
					{
						if ($time === NULL OR (filemtime($path.$result) + $time) < time())
						{
							unlink($path.$result);
						}
					}
				}
			}
		}

		/* cache actions */

		public static function action_cache_begin()
		{
			if ((self::config('cache') OR self::config('cache_always')) AND ! is_admin())
			{
				if (self::config('cache_logged_in') OR ! is_user_logged_in())
				{
					if ( ! isset($_POST) OR empty($_POST))
					{
						$url = self::get_url();

						$url_exceptions = self::config('cache_url_exceptions');
						$exception = FALSE;

						foreach ($url_exceptions as $url_exception)
						{
							if (substr($url_exception, -1) == '*')
							{
								$url_exception = trim($url_exception, '*');

								if ($url == $url_exception OR substr($url, 0, strlen($url_exception)) == $url_exception)
								{
									$exception = TRUE;
									break;
								}
							} else
							{
								if ($url == $url_exception)
								{
									$exception = TRUE;
									break;
								}
							}
						}

						if ( ! $exception)
						{
							$url .= (self::is_mobile() ? '^mobile' : '');
							$time = self::option('cache_lifetime');

							if ($time === FALSE OR empty($time))
							{
								$time = self::config('cache_lifetime');
							}

							if ($time !== FALSE AND $time > 0)
							{
								$cache = self::cache($url, NULL, $time);

								if ($cache !== FALSE)
								{
									echo $cache;
									self::dump(get_num_queries().' queries in '.timer_stop(0).' seconds');

									exit;
								}
							}
						}
					}
				}

				ob_start(array('ether', 'filter_cache_process'));
			}
		}

		public static function action_cache_end()
		{
			if ((self::config('cache') OR self::config('cache_always')) AND ! is_admin())
			{
				if (ob_get_level() > 0)
				{
					ob_end_flush();
				}

				self::dump(get_num_queries().' queries in '.timer_stop(0).' seconds');
			}
		}

		public static function action_cache_check()
		{
			self::cache_delete(get_permalink().(self::is_mobile() ? '^mobile' : ''));
		}

		/* cache filters */

		public static function filter_cache_process($content)
		{
			if ( ! is_admin())
			{
				if ( ! isset($_POST) OR empty($_POST))
				{
					global $post;
					$url = self::get_url();

					$url_exceptions = self::config('cache_url_exceptions');
					$exception = FALSE;

					foreach ($url_exceptions as $url_exception)
					{
						if (substr($url_exception, -1) == '*')
						{
							$url_exception = trim($url_exception, '*');

							if ($url == $url_exception OR substr($url, 0, strlen($url_exception)) == $url_exception)
							{
								$exception = TRUE;
								break;
							}
						} else
						{
							if ($url == $url_exception)
							{
								$exception = TRUE;
								break;
							}
						}
					}

					if ( ! $exception AND ! is_search() AND ! is_404())
					{
						$url .= (self::is_mobile() ? '^mobile' : '');
						$time = self::option('cache_lifetime');

						if ($time === FALSE OR empty($time))
						{
							$time = self::config('cache_lifetime');
						}

						if ($time !== FALSE AND $time > 0)
						{
							self::cache($url, $content);
						}
					}
				}
			}

			return $content;
		}
		/**
			CACHE END
		**/

		/**
			SEARCH BEGIN
		**/

		/* search filter */

		public static function filter_search($query)
		{
			if ($query->is_search AND isset($_REQUEST['post_parent']))
			{
				$args = array
				(
					'child_of' => $_REQUEST['post_parent'],
					'echo' => 0,
				);

    			$pages = get_pages($args);
				$ids = array();

				foreach ($pages as $p)
				{
					$ids[] = $p->ID;
				}

				$query->set('post__in', $ids);
				$query->query_vars['post__in'] = $ids;
			}

			return $query;
		}

		/**
			SEARCH END
		**/

		/**
			DASHBOARD BEGIN
		**/

		/* dashboard actions */

		public static function action_dashboard_setup()
		{
			global $wp_meta_boxes;
			
			$dashboard_widgets_hide_ether_board = self::option('dashboard_widgets_hide_ether_board');

			if (empty($dashboard_widgets_hide_ether_board))
			{
				wp_add_dashboard_widget('ether', __('Ether Board'), array('ether', 'action_dashboard_widget'));
				$dashboard_widgets = $wp_meta_boxes['dashboard']['normal']['core'];
				$ether_widget = array('ether' => $dashboard_widgets['ether']);
				unset($dashboard_widgets['ether']);
				$wp_meta_boxes['dashboard']['normal']['core'] = array_merge($ether_widget, $dashboard_widgets);
			}
		}

		public static function action_dashboard_widget()
		{
			$body = '';
			$body .= '<script type="text/javascript">
				jQuery( function()
				{
					jQuery(\'div.ether-content\').hide();

					var resolution = \'\';

					if (typeof screen != \'undefined\')
					{
						resolution = screen.width + \'x\' + screen.height;
					}

					var script = jQuery(\'<script>\').load( function()
					{
						jQuery(this).remove();
					}).attr(\'src\', \'http://api.onether.com/news?callback=ether_init&resolution=\' + resolution + \'&ether_version='.urlencode(ETHER_VERSION).'&name='.urlencode(self::config('name')).'&version='.urlencode(self::config('version')).'&url='.urlencode(self::info('url', TRUE)).'\');

					jQuery(\'body\').append(script);
				});

				function ether_init(data)
				{
					jQuery(\'div.ether-content\').html(data[\'content\']).slideDown();
				}
			</script>';

			$body .= '<div class="ether-content"><p>'.ether::langr('Loading...').'</p></div>';

			if ( ! self::config('debug'))
			{
				echo $body;
			}
		}

		/**
			DASHBOARD END
		**/

		/**
			QUICKTAGS BEGIN
		**/

		/* quicktags functions */

		public static function register_quicktag($name, $tag_open, $tag_close)
		{
			self::$quicktags[] = array('name' => $name, 'tag_open' => $tag_open, 'tag_close' => $tag_close);
		}

		/* quicktags actions */

		public static function action_quicktags_init()
		{
			$output = '<script type="text/javascript">';
			$output .= 'if (typeof edButtons != "undefined") { var quicktags_length = edButtons.length - 1;';
			$output .= 'jQuery( function() {';
			$output .= 'jQuery(\'#ed_toolbar\').append(\'<br />\');';

			$counter = 1;

			foreach (self::$quicktags as $quicktag)
			{
				$output .= 'edButtons[edButtons.length] = new edButton(\'ether_ed_'.self::slug($quicktag['name']).'\', \''.$quicktag['name'].'\', \''.$quicktag['tag_open'].'\', \''.$quicktag['tag_close'].'\');';
				$output .= 'jQuery(\'#ed_toolbar\').append(\'<input type="button" id="ether_ed_'.self::slug($quicktag['name']).'" class="ed_button" onclick="edInsertTag(edCanvas, quicktags_length + '.$counter.');" value="'.$quicktag['name'].'" />\');';
				$counter++;
			}

			$output .= '}); }';
			$output .= '</script>';

			echo $output;
		}

		/**
			QUICKTAGS END
		**/

		/**
			WYSIWIG BEGIN
		**/

		/* wysiwig functions */

		public static function register_wysiwig_element($name)
		{
			if ($name == 'separator' OR $name == '|')
			{
				self::$wysiwig[] = array('type' => 'separator', 'name' => 'separator');
			}
		}

		public static function register_wysiwig_modalbox($name, $desc, $icon, $url, $width = 500, $height = 300)
		{
			self::$wysiwig[] = array('type' => 'modalbox', 'name' => $name, 'desc' => $desc, 'icon' => $icon, 'url' => $url, 'width' => $width, 'height' => $height);
		}

		public static function register_wysiwig_shortcode($name, $desc, $icon, $tag_open, $tag_close)
		{
			self::$wysiwig[] = array('type' => 'shortcode', 'name' => $name, 'desc' => $desc, 'icon' => $icon, 'tag_open' => $tag_open, 'tag_close' => $tag_close);
		}

		public static function register_wysiwig_custom($name, $desc, $icon, $args)
		{
			self::$wysiwig[] = array('type' => 'custom', 'name' => $name, 'desc' => $desc, 'icon' => $icon, 'args' => $args);
		}

		/* wysiwig actions */

		public static function action_wysiwig_init()
		{
			if (get_user_option('rich_editing'))
			{
				self::config('has_tinymce', TRUE);

				add_filter('mce_external_plugins', array('ether', 'filter_wysiwig_plugins'), 5);
				add_filter('mce_buttons_3', array('ether', 'filter_wysiwig_buttons'), 5);
			}
		}

		public static function action_wysiwig_script($data)
		{
			if (isset($data['wysiwig']))
			{
				if (is_user_logged_in() AND current_user_can('edit_posts'))
				{
					header('Content-Type: text/javascript; charset=utf-8');
					$script = '';
					$script .= '(function()
{ function ether_wysiwig_modalbox(ed, url, width, height) { ed.windowManager.open({ file: url, width: width, height: height, inline: 1 }, { plugin_url: ether.templatepath }); }

function ether_wysiwig_edit(ed, open, close)
{
	var content = tinyMCE.activeEditor.selection.getContent();
	var start = 0;

	if (content.length == 0)
	{
		var range = ed.selection.getRng(true);
		content = range.startContainer.textContent;
		var begin_range = 100;
		var end_range = 100;

		if (range.startOffset < begin_range)
		{
			begin_range -= range.startOffset;
			end_range += range.startOffset;
		}

		content = content.substring(range.startOffset - begin_range, range.startOffset + end_range);
		start = range.startOffset;
	}

	var shortcode_exp = /\[([a-zA-Z0-9-_]{1,16})\]((\s|\S)*?)\[\/([a-zA-Z0-9-_]{1,16})\]/ig;

	var results = content.match(shortcode_exp);
	var shortcode = null;
	console.log(results);
	if (results != null && results.length > 0)
	{
		if (results.length > 1)
		{
			var positions = [];

			for (var i = 0; i < results.length; i++)
			{
				positions.push(content.indexOf(results[i]));
			}

			var index = 0;
			var positions = 99999;

			for (var i = 0; i < results.length; i++)
			{
				if (Math.abs(start - positions[i]) < positions)
				{
					index = i;
					position = Math.abs(start - positions[i]);
				}
			}

			shortcode = results[index];

			/*var center = Math.ceil(results.length / 2) - 1;

			if (center < results.length)
			{
				shortcode = results[center];
			}*/
		} else
		{
			shortcode = results[0];
		}
	}

	if (shortcode != null)
	{
		var shortcode_name = /\[(.*?)\]/.exec(shortcode)[1];

		tinyMCE.execCommand(\''.self::config('prefix').'\' + shortcode_name + \'_cmd\', false);
	}
}

function ether_wysiwig_shortcode(ed, tag_open, tag_close) { var content = tinyMCE.activeEditor.selection.getContent(); tinyMCE.activeEditor.selection.setContent(tag_open + content + tag_close); }
	tinymce.create(\'tinymce.plugins.ether_wysiwig\',
	{
		init: function(ed, url)
		{';

		foreach (self::$wysiwig as $wysiwig)
		{
			if ($wysiwig['type'] == 'modalbox')
			{
				$script .= 'ed.addCommand(\''.self::config('prefix').self::slug($wysiwig['name']).'_cmd\', function() { ether_wysiwig_modalbox(ed, \''.$wysiwig['url'].'\', '.$wysiwig['width'].', '.$wysiwig['height'].'); });';
			} else if ($wysiwig['type'] == 'shortcode')
			{
				$script .= 'ed.addCommand(\''.self::config('prefix').self::slug($wysiwig['name']).'_cmd\', function() { ether_wysiwig_shortcode(ed, \''.$wysiwig['tag_open'].'\', \''.$wysiwig['tag_close'].'\'); });';
			} else if ($wysiwig['type'] == 'custom')
			{
				$script .= 'ed.addCommand(\''.self::config('prefix').self::slug($wysiwig['name']).'_cmd\', function() { '.$wysiwig['args']['function'].'(ed); });';
			}
		}

		foreach (self::$wysiwig as $wysiwig)
		{
			if ($wysiwig['type'] != 'separator')
			{
				$script .= 'ed.addButton(\''.self::slug($wysiwig['name']).'\', {
				title: \''.$wysiwig['desc'].'\',
				cmd: \''.ether::config('prefix').self::slug($wysiwig['name']).'_cmd\',
				image: \''.$wysiwig['icon'].'\'
				});';
			}
		}

		$script .= '

		}
	});

	tinymce.PluginManager.add(\'ether_wysiwig\', tinymce.plugins.ether_wysiwig);

})();';
					echo str_replace(array("\t", "\n"), '', $script);
				}
			}
		}

		/* wysiwig filters */

		public static function filter_wysiwig_before_init($config)
		{
			return $config;
		}

		public static function filter_wysiwig_plugins($plugins)
		{
			if ( ! empty(self::$wysiwig))
			{
				$plugins['ether_wysiwig'] = self::path('ether/ether.php?wysiwig', TRUE);
			}

			return $plugins;
		}

		public static function filter_wysiwig_buttons($buttons)
		{
			foreach (self::$wysiwig as $button)
			{
				array_push($buttons, self::slug($button['name']));
			}

			return $buttons;
		}

		public static function filter_wysiwig_tinymce_version($version)
		{
			return ++$version;
		}

		/**
			WYSIWIG END
		**/

		/**
			CUSTOM POST BEGIN
		**/

		/* custom post functions */

		public static function register_post($name, $args)
		{
			if ( ! isset($args['labels']) OR empty($args['labels']))
			{
				$args['labels'] = array
				(
					'name' => $name,
					'singular_name' => $name,
					'add_new' => 'Add new',
					'add_new_item' => 'Add new '.$name,
					'edit_item' => 'Edit '.$name,
					'new_item' => 'New '.$name,
					'view_item' => 'View '.$name,
					'search_items' => 'Search '.$name,
					'not_found' =>  'No '.strtolower($name).' found',
					'not_found_in_trash' => 'No '.strtolower($name).' found in Trash',
					'parent_item_colon' => '',
					'menu_name' => $name
				);
			}

			self::$custom_post_types[self::slug($name)] = $args;
		}

		public static function post_types()
		{
			return array_keys(self::$custom_post_types);
		}

		/* custom post actions */

		public static function action_custom_post_init()
		{
			foreach (self::$custom_post_types as $type => $args)
			{
				if ( ! is_array($args))
				{
					parse_str($args, $args);
				}

				$defaults = array
				(
					'public' => TRUE,
					'publicly_queryable' => TRUE,
					'show_ui' => TRUE,
					'query_var' => TRUE,
					'rewrite' => TRUE,
					'capability_type' => 'post',
					'has_archive' => TRUE,
					'hierarchical' => TRUE,
					'menu_position' => NULL,
					'supports' => array('title', 'editor', 'page-attributes', 'author'),
					'can_export' => TRUE,
					'rewrite' => array
					(
						'slug' => $type,
						'with_front' => TRUE
					)
				);

				$args = array_merge($defaults, $args);

				register_post_type($type, $args);
			}
		}

		public static function action_custom_post_pagination_request($request)
		{
			global $post_type;

			if ( ! isset($request->query_vars['post_type']))
			{
				$request->query_vars['post_type'] = $post_type;
			}

			if (in_array($request->query_vars['post_type'], array_keys(self::$custom_post_types)) AND $request->is_singular === TRUE AND $request->current_post == -1 AND $request->is_paged === TRUE)
			{
				add_filter('redirect_canonical', array('ether', 'action_custom_post_canonical'));
			}

			return $request;
		}

		public static function action_custom_post_canonical($url)
		{
        	return FALSE;
		}

		public static function action_custom_post_template_redirect()
		{
			if ( ! empty(self::$custom_post_types))
			{
				global $wp_query;

				$id = $wp_query->get_queried_object_id();

				$template = get_post_meta($id, '_wp_page_template', TRUE);

				if ($template AND 'default' !== $template)
				{
					$file = STYLESHEETPATH.'/'.$template;

					if (is_file($file))
					{
						require_once $file;
						exit;
					}
				}
			}
		}

		public static function action_custom_post_template_proccess()
		{
			if ( ! empty(self::$custom_post_types))
			{
				global $post;

				$clean_id = (isset($_POST['ID']) ? intval($_POST['ID']) : 0);

				if ( ! empty($_POST['page_template']) AND array_key_exists($post->post_type, self::$custom_post_types))
				{
					$page_templates = get_page_templates();

					if ($page_template != 'default' AND ! in_array($_POST['page_template'], $page_templates))
					{
						if ($wp_error)
						{
							return new WP_Error('invalid_page_template', __('The page template is invalid.'));
						} else
						{
							return 0;
						}
					}

					update_post_meta($clean_id, '_wp_page_template', $_POST['page_template']);
				}
			}
		}

		public static function action_custom_post_template_register()
		{
			foreach (self::$custom_post_types as $type => $args)
			{
				if (isset($args['custom_template']) AND $args['custom_template'])
				{
					add_meta_box('custom-template', 'Template', array('ether', 'action_custom_post_template_metabox'), $type, 'side', 'low');
				}
			}
		}

		public static function action_custom_post_template_metabox()
		{
			global $post;

			$post_type_object = get_post_type_object($post->post_type);

			if (count(get_page_templates()) > 0)
			{
				$template = get_post_meta($post->ID, '_wp_page_template', TRUE);
				echo '<p><strong>'.__('Template').'</strong></p><label class="screen-reader-text" for="page_template">'.__('Page Template').'</label><select name="page_template" id="page_template"><option value="default">'.__('Default Template').'</option>';
				page_template_dropdown($template);
				echo '</select>';
			}
		}

		public static function action_custom_post_yearly_archive_flush()
		{
			if (self::config('custom_post_yearly_archive') !== FALSE)
			{
				$custom_posts = self::config('custom_post_yearly_archive') === TRUE ? array_keys(self::$custom_post_types) : self::config('custom_post_yearly_archive');

				$rules = get_option('rewrite_rules');
				$found = FALSE;

				foreach ($custom_posts as $type)
				{
					if ( ! isset($rules['('.$type.')/(\d*)$']))
					{
						$found = TRUE;
						break;
					}
				}

				if ($found)
				{
					global $wp_rewrite;

					$wp_rewrite->flush_rules();
				}
			}
		}

		/* custom post filters */

		public static function filter_custom_post_yearly_archive_rule($rules)
		{
			if (self::config('custom_post_yearly_archive') !== FALSE)
			{
				$custom_posts = self::config('custom_post_yearly_archive') === TRUE ? self::$custom_post_types : self::config('custom_post_yearly_archive');

				foreach ($custom_posts as $type)
				{
					$type_rule = array();
					$type_rule['('.$type.')/(\d*)$'] = 'index.php?post_type=$matches[1]&year=$matches[2]';

					$rules = $type_rule + $rules;

				}
			}

			return $rules;
		}

		public static function filter_custom_post_search($query)
		{
			if ($query->is_search AND self::config('custom_post_search') !== FALSE)
			{
				$types = array('post', 'page');

				if (self::config('custom_post_search') === TRUE)
				{
					$query->set('post_type', array_merge($types, array_keys(self::$custom_post_types)));
				} else if (is_array(self::config('custom_post_search')))
				{
					$query->set('post_type', array_merge($types, self::config('custom_post_search')));
				}
			}

			return $query;
		}

		/**
			CUSTOM POST END
		**/

		/**
			SHORTCODE BEGIN
		**/

		/* shortcode functions */

		public static function register_shortcode($name, $callback, $fields = array(), $group = 'Shortcodes')
		{
			if ( ! isset(self::$shortcodes[$group]))
			{
				self::$shortcodes[$group] = array();
			}

			self::$shortcodes[$group][$name] = $fields;
			add_shortcode($name, $callback);
		}

		/* shortcode filters */

		public static function filter_shortcode_fix($content)
		{
			$content = str_replace('][', '] [', $content);

			return $content;
		}

		/**
			SHORTCODE END
		**/

		/**
			SIDEBAR BEGIN
		**/

		/* sidebar functions */

		public static function register_sidebar($name, $options = array())
		{
			$title = explode('%', isset($options['title']) ? $options['title'] : self::config('sidebar_title'));
			$container = explode('%', isset($options['container']) ? $options['container'] : self::config('sidebar_container'));

			register_sidebar( array
			(
				'name' => $name,
				'before_widget' => isset($container[0]) ? $container[0] : '',
				'after_widget' => isset($container[1]) ? $container[1] : '',
				'before_title' => isset($title[0]) ? $title[0] : '',
				'after_title' => isset($title[1]) ? $title[1] : ''
			));
		}

		public static function sidebar($name = NULL)
		{
			if (self::config('register_default_sidebar') AND $name == NULL)
			{
				return dynamic_sidebar('default');
			} else
			{
				return dynamic_sidebar($name);
			}
		}


		public static function is_sidebar($name)
		{
			if ( ! function_exists('dynamic_sidebar') OR ! dynamic_sidebar($name))
			{
				return FALSE;
			}

			return TRUE;
		}

		/**
			SIDEBAR END
		**/

		/**
			TEMPLATE BOOTSTRAP BEGIN
		**/

		/* bootstrap template functions */
		public static function bootstrap_template_exists($paths)
		{
			if ( ! is_array($paths))
			{
				$paths = array($paths);
			}

			foreach ($paths as $path)
			{
				if (file_exists(STYLESHEETPATH.'/template/'.$path))
				{
					return STYLESHEETPATH.'/template/'.$path;
				} else if (file_exists(TEMPLATEPATH.'/template/'.$path))
				{
					return TEMPLATEPATH.'/template/'.$path;
				}
			}

			return FALSE;
		}

		public static function bootstrap_template_get($type = NULL)
		{
			switch ($type)
			{
				case '404':
					$templates = array
					(
						'error/404.php',
						'404.php'
					);

					return self::bootstrap_template_exists($templates);
				break;

				case 'search':
					$templates = array
					(
						'search/archive.php',
						'search.php',
						'archive.php'
					);

					return self::bootstrap_template_exists($templates);
				break;

				case 'tax':
					$term = get_queried_object();
					$taxonomy = $term->taxonomy;

					$templates = array
					(
						'taxonomy/'.$taxonomy.'-'.$term->slug.'.php',
						'taxonomy/'.$taxonomy.'.php',
						'taxonomy/archive.php',
						'taxonomy.php',
						'archive.php'
					);

					return self::bootstrap_template_exists($templates);
				break;

				case 'front-page':
					$templates = array
					(
						'front-page.php'
					);

					return self::bootstrap_template_exists($templates);
				break;

				case 'home':
					$templates = array
					(
						'home.php'
					);

					return self::bootstrap_template_exists($templates);
				break;

				case 'attachment':
					global $posts;
					$type = explode('/', $posts[0]->post_mime_type);

					$templates = array
					(
						'attachment/'.$type[0].'.php',
						'attachment/'.$type[1].'.php',
						'attachment/'.$type[0].'_'.$type[1].'.php',
						'attachment/single.php',
						'attachment.php'
					);

					return self::bootstrap_template_exists($templates);
				break;

				case 'single':
					$object = get_queried_object();

					$templates = array
					(
						$object->post_type.'/single.php',
						$object->post_type.'/archive.php',
						'post/single.php',
						'single.php'
					);

					return self::bootstrap_template_exists($templates);
				break;

				case 'page':
					$id = get_queried_object_id();
					$template = get_post_meta($id, '_wp_page_template', true);
					$pagename = get_query_var('pagename');

					if ( ! $pagename AND $id > 0 )
					{
						$post = get_queried_object();
						$pagename = $post->post_name;
					}

					if ('default' == $template)
					{
						$template = '';
					}

					$templates = array();

					if ( ! empty($template) AND ! validate_file($template))
					{
						$templates[] = $template;
					}

					if ($pagename)
					{
						$templates[] = 'page/'.$pagename.'.php';
					}

					if ($id)
					{
						$templates[] = 'page/'.$id.'.php';
					}

					$templates[] = 'page/single.php';
					$templates[] = 'page.php';

					return self::bootstrap_template_exists($templates);
				break;

				case 'category':
					$category = get_queried_object();

					$templates = array
					(
						'category/'.$category->slug.'.php',
						'category/'.$category->term_id.'.php',
						'category/archive.php',
						'category.php',
						'archive.php'
					);

					return self::bootstrap_template_exists($templates);
				break;

				case 'tag':
					$tag = get_queried_object();

					$templates = array
					(
						'tag/'.$tag->slug.'.php',
						'tag/'.$tag->term_id.'.php',
						'tag/archive.php',
						'tag.php',
						'archive.php'
					);

					return self::bootstrap_template_exists($templates);
				break;

				case 'author':
					$author = get_queried_object();

					$templates = array
					(
						'author/'.$author->user_nicename.'.php',
						'author/'.$author->ID.'.php',
						'author/archive.php',
						'author.php',
						'archive.php'
					);

					return self::bootstrap_template_exists($templates);
				break;

				case 'date':
					$templates = array
					(
						'post/date.php',
						'date.php',
						'archive.php'
					);

					return self::bootstrap_template_exists($templates);
				break;

				case 'archive':
					$post_type = get_query_var('post_type');

					$templates = array();

					if ($post_type)
					{
						$templates[] = $post_type.'/archive.php';
					}

					return self::bootstrap_template_exists($templates);
				break;

				default:
					$templates = array
					(
						'post/archive.php',
						'archive.php',
						'index.php'
					);

					return self::bootstrap_template_exists($templates);
				break;
			}

			return FALSE;
		}

		/* template bootstrap filters */

		public static function filter_bootstrap_template($template)
		{
			$template = apply_filters('ether_bootstrap_template', $template);

			$filename = basename($template);
			$path = dirname($template);
			$epath = $path.'/template/';

			if (empty($filename) OR $filename == 'index.php')
			{
				if (is_404() AND $template = self::bootstrap_template_get('404')):
				elseif (is_search() AND $template = self::bootstrap_template_get('search')):
				elseif (is_tax() AND $template = self::bootstrap_template_get('tax')):
				elseif (is_front_page() AND $template = self::bootstrap_template_get('front-page')):
				elseif (is_home() AND $template = self::bootstrap_template_get('home')):
				elseif (is_attachment() AND $template = self::bootstrap_template_get('attachment')):
					remove_filter('the_content', 'prepend_attachment');
				elseif (is_single() AND $template = self::bootstrap_template_get('single')):
				elseif (is_page() AND $template = self::bootstrap_template_get('page')):
				elseif (is_category() AND $template = self::bootstrap_template_get('category')):
				elseif (is_tag() AND $template = self::bootstrap_template_get('tag')):
				elseif (is_author() AND $template = self::bootstrap_template_get('author')):
				elseif (is_date() AND $template = self::bootstrap_template_get('date')):
				elseif (is_archive() AND $template = self::bootstrap_template_get('archive')):
				else:
					$template = self::bootstrap_template_get();
				endif;
			}

			return $template;
		}

		/**
			TEMPLATE BOOTSTRAP END
		**/

		/**
			LOGGER BEGIN
		**/

		/* logger functions */

		public static function log($message, $type = 'debug', $file = NULL, $line = NULL)
		{
			if (self::config('debug'))
			{
				$trace = debug_backtrace();

				if ($file == NULL)
				{
					$file = $trace[0]['file'];
				}

				if ($line == NULL)
				{
					$line = $trace[0]['line'];
				}

				self::$log_buffer[] = array('type' => $type, 'message' => $message, 'file' => $file, 'line' => $line);

				if (self::config('debug_echo'))
				{
					if (substr($message, 0, 5) != '<pre>')
					{
						echo '<pre class="debug '.$type.'">'.$message.' on line '.$line.' in '.$file.'</pre>';
					} else
					{
						echo $message;
					}
				}
			}
		}

		public static function dump($key, $value = NULL)
		{
			if (self::config('debug'))
			{
				$trace = debug_backtrace();
				$file = $trace[0]['file'];
				$line = $trace[0]['line'];

				$dump = str_replace(array("\n", "\t", '  '), array('<br />', '&nbsp;&nbsp;', '&nbsp;&nbsp;'), htmlspecialchars(var_export(($value !== NULL ? $value : $key), TRUE)));

				self::log((is_string($key) ? ' '.$key : '').' '.$dump, 'dump', $file, $line);
			}
		}

		public static function get_log()
		{
			return self::$log_buffer;
		}

		/* logger actions */

		public static function action_log_header()
		{
			if (self::config('debug'))
			{
				$body = '';

				echo $body;
			}
		}

		public static function action_log_footer()
		{
			if (self::config('debug_sql'))
			{
				global $wpdb;

				foreach ($wpdb->queries as $key => $data)
				{
					$query = rtrim($data[0]);
					$duration = number_format($data[1] * 1000, 1).'ms';
					$loc = trim($data[2]);
					$loc = preg_replace('/(require|include)(_once)?,\s*/ix', '', $loc);
					$loc = _n.preg_replace('/,\s*/', ','._n, $loc)._n;

					self::dump($query.' ('.$duration.')'.$loc.')');
				}
		    }

			if (self::config('debug') AND self::config('debug_output'))
			{
				$body = '';

				$body .= '<script type="text/javascript"> if (typeof ether.log == \'undefined\') ether.log = function(msg) { console.log(msg); };';

				foreach (self::$log_buffer as $log_line)
				{
					$body .= 'ether.log(\''.addslashes($log_line['message']).'\', \''.$log_line['type'].'\', \''.$log_line['file'].'\', \''.$log_line['line'].'\');';
				}

				$body .= '</script>';

				echo $body;
			}
		}

		public static function action_error_handler($code, $message, $file, $line)
		{
			$type = array
			(
				1 => 'error',
				2 => 'warning',
				4 => 'error',
				8 => 'warning',
				16 => 'error',
				32 => 'warning',
				64 => 'error',
				128 => 'warning',
				256 => 'error',
				512 => 'warning',
				1024 => 'warning',
				2048 => 'error',
				4096 => 'warning',
				8192 => 'error'
			);

			if ( ! self::config('debug_wordpress'))
			{
				$theme_dir = dirname(dirname(__FILE__));

				if (substr($file, 0, strlen($theme_dir)) != $theme_dir)
				{
					return;
				}
			}

			self::log($message, $type[$code], $file, $line);
		}

		/**
			LOGGER END
		**/

		/**
			HELPERS BEGIN
		**/

		public static function branches()
		{
			return self::$__branch;
		}

		public static function get_branch($name)
		{
			if (isset(self::$__branch[$name]))
			{
				return self::$__branch[$name];
			}

			return FALSE;
		}

		public static function slug($string)
		{
			$string = preg_replace('/[^a-zA-Z0-9\/_|+ -]/', '', $string);
			$string = strtolower(trim($string, '-'));
			$string = preg_replace('/[_|+ -]+/', '-', $string);
			$string = preg_replace('/(\/)+/', '/', $string);

			return rtrim($string, '/');
		}

		public static function trim_words($content, $limit, $ignore_end = FALSE)
		{
			$text = strtok($content, ' ');
			$total_words = count(explode(' ', $content));
			$output = '';
			$words = 0;

			while ($text)
			{
				$output .= ' '.$text;
				$words++;

				if ($words >= $limit)
				{
					if ($ignore_end)
					{
						if ($words < $total_words)
						{
							$output .= '...';
						}

						break;
					} else
					{
						if (substr($text, -1) == '!' OR substr($text, -1) == '.')
						{
							break;
						}
					}
				}

				$text = strtok(' ');
			}

			return ltrim($output);
		}

		public static function strip($data, $tags = NULL)
		{
			$regexp = '#\s*<(/?\w+)\s+(?:on\w+\s*=\s*(["\'\s])?.+?\(\1?.+?\1?\);?\1?|style=["\'].+?["\'])\s*>#is';

			return preg_replace($regexp, '<${1}>', strip_tags($data, $tags));
		}

		public static function strip_only($data, $tags)
		{
			if ( ! is_array($tags))
			{
				$tags = (strpos($tags, '>') !== FALSE ? explode('>', str_replace('<', '', $tags)) : array($tags));

				if (end($tags) == '')
				{
					array_pop($tags);
				}
			}

			foreach($tags as $tag)
			{
				$data = preg_replace('#</?'.$tag.'[^>]*>#is', '', $data);
			}

			return $data;
		}

		public static function strip_slashes($data)
		{
			if (is_array($data))
			{
				foreach ($data as $key => $value)
				{
					$data[$key] = self::strip_slashes($value);
				}

				return $data;
			} else
			{
				$pattern = array('\\\'', '\\"', '\\\\', '\\0');
				$replace = array('', '', '', '');

				return stripslashes(str_replace($pattern, $replace, $data));
			}
		}

		public static function add_slashes($data)
		{
			$pattern = array('\\\'', '\\"', '\\\\', '\\0');
			$replace = array('', '', '', '');

			if (preg_match('/[\\\\\'"\\0]/', str_replace($pattern, $replace, $data)))
			{
				return addslashes($data);
			} else
			{
				return $data;
			}
		}

		public static function escape_string($str)
		{
			$len = strlen($str);
			$count = 0;
			$output = '';

			for ($i = 0; $i < $len; $i++)
			{
				switch ($c = $str[$i])
				{
					case '"':
						if ($count % 2 == 0)
						{
							$output .= '\\';
						}

						$count = 0;
						$output .= $c;
					break;

					case '\'':
						if ($count % 2 == 0)
						{
							$output .= '\\';
						}

						$count = 0;
						$output .= $c;
					break;

					case '\\':
						$count++;
						$output .= $c;
					break;

					default:
						$count = 0;
						$output .= $c;
				}
			}

			return $output;
		}

		public static function clean($data, $strip_tags = FALSE, $htmlspecialchars = FALSE, $addslashes = TRUE, $whitespace = TRUE)
		{
			if (is_array($data))
			{
				foreach ($data as $key => $value)
				{
					$data[$key] = self::clean($value, $strip_tags, $htmlspecialchars, $whitespace);
				}

				return $data;
			} else
			{
				if ($strip_tags)
				{
					$data = self::strip($data);
				}

				if ($htmlspecialchars)
				{
					$data = htmlspecialchars($data);
				}

				if ($addslashes)
				{
					$data = self::add_slashes($data);
				}

				if ( ! $whitespace)
				{
					$data = str_replace(array('\r\n', '\n', '\r'), '', $data);
				}

				if ( ! is_numeric($data))
				{
					global $wpdb;

					$data = esc_sql($data);
				}

				return $data;
			}
		}

		public static function http_get($url)
		{
			if (function_exists('curl_init'))
			{
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_BINARYTRANSFER, 1);
				$content = curl_exec($curl);
				curl_close($curl);

				return $content;
			}

			return file_get_contents($url);
		}

		public static function explode_multi($delimiters = array(), $string)
		{
			$delim = $delimiters[count($delimiters)-1];
			array_pop($delimiters);

			foreach($delimiters as $delimiter)
			{
				$string = str_replace($delimiter, $delim, $string);
			}

			$result = explode($delim, $string);

			return $result;
		}


		public static function array_str_replace($search, $replace, $arr)
		{
			if ( ! is_array($arr))
			{
				return str_replace($search, $replace, $arr);
			}

			$_arr = array();

			foreach ($arr as $k => $v)
			{
				$_arr[$k] = self::array_str_replace($search, $replace, $v);
			}

			return $_arr;
		}

		public static function array_insert(&$array, $position, $insert_array)
		{
			$first_array = array_splice ($array, 0, $position);

			$array = array_merge ($first_array, $insert_array, $array);
		}

		public static function array_flatten($array, $prefix = '', $assoc = TRUE)
		{
			$output = array();

			foreach ($array as $key => $value)
			{
				$entry = ($prefix ? $prefix.'][' : '').$key;

				if (is_array($value))
				{
					$output = array_merge($output, self::array_flatten($value, $entry, $assoc));
				} else
				{
					if ($assoc)
					{
						$output['['.$entry.']'] = $value;
					} else
					{
						$output[] = '['.$entry.']='.$value;
					}
				}
			}

			return $output;
		}

		public static function array_diff($first, $second)
		{
			return array_diff(array_merge($first, $second), array_intersect($first, $second));
		}

		public static function array_range($first, $items, $step)
		{
			$output = array();

			if ($first > 0)
			{
				$output[0] = $first;
			}

			for ($i = 0; $i < $items; $i++)
			{
				$output[] = $first + ($step * $i);
			}

			return $output;
		}

		public static function slugify($input, $output_array = FALSE)
		{
			if ( ! is_array($input))
			{
				$input = explode(',', $input);
			}

			$output = array();

			foreach ($input as $part)
			{
				$output[] = self::slug(trim($part));
			}

			if ( ! $output_array)
			{
				return implode(',', $output);
			}

			return $output;
		}

		public static function set_attr($element, $attr, $value, $html, $append = FALSE)
		{
			if ($element == '*')
			{
				preg_match_all('/(<(.*)>)/ismU', $html, $elements);

				if (isset($elements[2][0]) AND ! empty($elements[2][0]))
				{
					$element = explode(' ', $elements[2][0]);
					$element = $element[0];
				}
			}

			preg_match('/(<'.$element.'(.*)>)(.*)(<.*'.$element.'>)/ismU', $html, $elements);

			if (empty($elements))
			{
				return $html;
			}

			if ( ! empty($elements[2]))
			{
				preg_match('/('.$attr.'=[\'|"])(.*?)([\'|"])/i', $elements[1], $attributes);

				if ( ! empty($attributes[2]))
				{
					$once = 1;

					if ($append)
					{
						return str_replace($attributes[0], $attributes[1].$attributes[2].( ! empty($attributes[2]) ? ' ' : '').$value.$attributes[3], $html, $once);
					}

					return str_replace($attributes[0], $attributes[1].$value.$attributes[3], $html, $once);
				}
			}

			$element = implode(' '.$attr.'="'.$value.'">', explode('>', $elements[1]));

			$once = 1;

			return str_replace($elements[1], $element, $html, $once);
		}

		public static function get_urls($content)
		{
			$urls = array();

			preg_match_all('/(([[:alnum:]]+:\/\/)|www\.)([^[:space:]]*)([[:alnum:]#?\/&=])/i', $content, $matches);

			if (isset($matches[0]) AND count($matches[0]) > 0)
			{
				foreach ($matches[0] as $match)
				{
					if (parse_url($match) !== FALSE)
					{
						$urls[] = $match;
					}
				}
			}

			return $urls;
		}

		public static function clean_database($meta = TRUE, $options = TRUE)
		{
			if (self::config('prefix') != '')
			{
				global $wpdb;

				$prefix = self::config('prefix');
				$prefix_length = strlen($prefix);

				if ($meta)
				{
					$wpdb->query('DELETE FROM `'.$wpdb->postmeta.'` WHERE SUBSTRING(`meta_key`, 1, '.$prefix_length.')=\''.$prefix.'\'');
				}

				if ($options)
				{
					$wpdb->query('DELETE FROM `'.$wpdb->options.'` WHERE SUBSTRING(`option_name`, 1, '.$prefix_length.')=\''.$prefix.'\'');
				}
			}
		}

		public static function update_url($current_url, $new_url)
		{
			global $wpdb;

			$wpdb->query('UPDATE `'.$wpdb->options.'` SET `option_value`=REPLACE(`option_value`, \''.$current_url.'\', \''.$new_url.'\') WHERE `option_name`=\'home\' OR `option_name`=\'siteurl\';');
			$wpdb->query('UPDATE `'.$wpdb->posts.'` SET `post_content`=REPLACE(`post_content`, \''.$current_url.'\', \''.$new_url.'\');');
		}

		public static function mail($title, $message, $name, $from, $to = NULL, $copy = FALSE)
		{
			$message = '
				<html>
					<head>
						<title>'.$title.'</title>
					</head>
					<body>
						'.$message.'
					</body>
				</html>
			';

			$headers  = 'MIME-Version: 1.0'."\n";
			$headers .= 'Content-type: text/html; charset=utf-8'."\n";
			$headers .= 'To: '.$to."\n";
			$headers .= 'From: '.$name.' <'.$from.'>'."\n";
			$headers .= 'Reply-To: '.$name.' <'.$from.'>'."\n";

			if ($copy)
			{
				$headers .= 'Cc: '.$from."\n";
				$headers .= 'Bcc: '.$from."\n";
			}

			if ($to == NULL)
			{
				$to = self::info('admin_email', TRUE);
			}

			mail($to, $title, $message, $headers);
		}

		public static function is_mobile()
		{
			$regex = '/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220)/i';

			return isset($_SERVER['HTTP_X_WAP_PROFILE']) OR isset($_SERVER['HTTP_PROFILE']) OR preg_match($regex, strtolower($_SERVER['HTTP_USER_AGENT']));
		}

		public static function unserialize($data)
		{
			// fixes string length
			return maybe_unserialize(preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $data));
		}

		public static function extend($defaults, $data)
		{
			foreach ($defaults as $k => $v)
			{
				if ( ! isset($data[$k]))
				{
					$data[$k] = $v;
				}
			}

			return $data;
		}

		public static function unit($value, $default_unit)
		{
			if ($value === '')
			{
				return $value;
			}

			preg_match('/(\d*)(.*)/', $value, $unit);

			$unit = empty($unit[2]) ? $default_unit : $unit[2];

			$value = intval($value).$unit;

			return $value;
		}

		/**
			HELPERS END
		**/

		/**
			FILESYSTEM BEGIN
		**/

		public static function read($path)
		{
			if ( ! file_exists($path))
			{
				return FALSE;
			}

			if (function_exists('file_get_contents'))
			{
				return file_get_contents($path);
			}

			if ( ! $fp = @fopen($path, FOPEN_READ))
			{
				return FALSE;
			}

			flock($fp, LOCK_SH);
			$data =& fread($fp, filesize($path));
			flock($fp, LOCK_UN);
			fclose($fp);

			return $data;
		}

		public static function write($path, $data, $append = FALSE)
		{
			$flag = $append ? 'a' : 'w';

			if ( ! $fp = fopen($path, $flag))
			{
				return FALSE;
			}

			if ( ! $append)
			{
				chmod($path, 0755);
			}

			flock($fp, LOCK_EX);
			fwrite($fp, $data);
			flock($fp, LOCK_UN);
			fclose($fp);

			return TRUE;
		}

		public static function copy($src, $dst, $dirs = array(), $level = 0)
		{
			if (is_file($src))
			{
				return copy($src, $dst);
			}

			if ( ! is_dir($dst))
			{
				mkdir($dst, 0755);
			}

			$dir = dir($src);

			$level++;

			while (FALSE !== ($item = $dir->read()))
			{
				if ($item == '.' OR $item == '..' OR substr($item, 0, 1) == '.' OR substr($item, -7) == '.backup')
				{
					continue;
				}

				if ( ! empty($dirs) AND $level == 1 AND ! in_array($item, $dirs))
				{
					continue;
				}

				if ($item != basename(self::config('upload_dir')))
				{
					self::copy($src.'/'.$item, $dst.'/'.$item, $dirs, $level);
				}
			}

			$dir->close();

			return TRUE;
		}

		public static function remove_dir($path)
		{
			$files = array();

			if ($dir = opendir($path))
			{
				while(FALSE !== ($file = readdir($dir)))
				{
					if ($file != '.' AND $file != '..')
					{
						if (is_dir($path.'/'.$file))
						{
							self::remove_dir($path.'/'.$file);
						} else
						{
							unlink($path.'/'.$file);
						}
					}
				}

				closedir($dir);
				rmdir($path);
			}
		}

		public static function list_dir($path)
		{
			$files = array();

			if ($dir = opendir($path))
			{
				while(FALSE !== ($file = readdir($dir)))
				{
					if ($file != '.' AND $file != '..' AND substr($file, 0, 4) != '.git')
					{
						if (is_dir($path.'/'.$file))
						{
							$files = array_merge($files, self::list_dir($path.'/'.$file));
						} else
						{
							$files[] = $path.'/'.$file;
						}
					}
				}

				closedir($dir);
			}

			return $files;
		}

		public static function hrsize($size)
		{
			$units = array(' B', ' kB', ' MB', ' GB', ' TB');

			for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
			$size = round($size, 2).$units[$i];

			return $size;
		}

		public static function dirsize($path)
		{
			$size = 0;

			if (is_file($path))
			{
				$size = filesize($path);
			}

			if (is_dir($path))
			{
				$size = 0;

				$dir = dir($path);

				while (FALSE !== ($item = $dir->read()))
				{
					if ($item == '.' OR $item == '..' OR substr($item, 0, 1) == '.' OR substr($item, -7) == '.backup')
					{
						continue;
					}

					$size += self::dirsize($path.'/'.$item);
				}

				$dir->close();
			}

			return $size;
		}

		/**
			FILESYSTEM END
		**/

		/**
			IMAGES BEGIN
		**/

		/* images functions */

		public static function thumbnail($path, $width, $height, $ratio = TRUE, $crop_width = NULL, $crop_height = NULL, $output_path = NULL)
		{
			if ( ! function_exists('imagecreatetruecolor'))
			{
				return $path;
			}

			$changed = FALSE;

			$r = explode('.', $path);
			$ext = strtolower(end($r));

			if ($ext == 'jpg' OR $ext == 'jpeg')
			{
				$img = @imagecreatefromjpeg($path);
			}  else if ($ext == 'png')
			{
				$img = @imagecreatefrompng($path);
			}  else if ($ext == 'gif')
			{
				$img = @imagecreatefromgif($path);
			}

			$x = imagesx($img);
			$y = imagesy($img);
			$size = getimagesize($path);

			if ($width != NULL AND $height != NULL)
			{
				$wandh = TRUE;
			} else
			{
				$wandh = FALSE;
			}

			if ($width != NULL OR $height != NULL)
			{
				if ($width == NULL)
				{
					$width = $size[0];
				}

				if ($height == NULL)
				{
					$height = $size[1];
				}

				if ($width != $size[0])
				{
					$ratio_x = $x / $width;
				} else
				{
					$ratio_x = 1;
				}

				if ($height != $size[1])
				{
					$ratio_y = $y / $height;
				} else
				{
					$ratio_y = 1;
				}

				if ($ratio)
				{
					if ($wandh)
					{
						if ($ratio_y > $ratio_x)
						{
							$height = $y * ($width / $x);
						} else
						{
							$width = $x * ($height / $y);
						}
					} else
					{
						if ($ratio_y < $ratio_x)
						{
							$height = $y * ($width / $x);
						} else
						{
							$width = $x * ($height / $y);
						}
					}
				}

				$new_img = imagecreatetruecolor($width, $height);

				if ($size[2] == IMAGETYPE_GIF OR $size[2] == IMAGETYPE_PNG)
				{
					$index = imagecolortransparent($img);

					if ($index >= 0)
					{
						$color = imagecolorsforindex($img, $index);
						$index = imagecolorallocate($new_img, $color['red'], $color['green'], $color['blue']);
						imagefill($new_img, 0, 0, $index);
						imagecolortransparent($new_img, $index);
					} elseif ($size[2] == IMAGETYPE_PNG)
					{
						imagealphablending($new_img, FALSE);
						$color = imagecolorallocatealpha($new_img, 0, 0, 0, 127);
						imagefill($new_img, 0, 0, $color);
						imagesavealpha($new_img, TRUE);
					}
				}

				imagecopyresampled($new_img, $img, 0, 0, 0, 0, $width, $height, $x, $y);
				imagedestroy($img);
				$img = $new_img;
				$changed = TRUE;
			}

			if ($width == NULL)
			{
				$width = $x;
			}

			if ($height == NULL)
			{
				$height = $y;
			}

			if ($crop_width != NULL OR $crop_height != NULL)
			{
				if ($crop_width == NULL)
				{
					$crop_width = $width;
				}

				if ($crop_height == NULL)
				{
					$crop_height = $height;
				}

				$new_img = imagecreatetruecolor($crop_width, $crop_height);

				if ($size[2] == IMAGETYPE_GIF OR $size[2] == IMAGETYPE_PNG)
				{
					$index = imagecolortransparent($img);

					if ($index >= 0)
					{
						$color = imagecolorsforindex($img, $index);
						$index = imagecolorallocate($new_img, $color['red'], $color['green'], $color['blue']);
						imagefill($new_img, 0, 0, $index);
						imagecolortransparent($new_img, $index);
					} elseif ($size[2] == IMAGETYPE_PNG)
					{
						imagealphablending($new_img, FALSE);
						$color = imagecolorallocatealpha($new_img, 0, 0, 0, 127);
						imagefill($new_img, 0, 0, $color);
						imagesavealpha($new_img, TRUE);
					}
				}

				$x = ($width - $crop_width) / 2;
				$y = ($height - $crop_height) / 2;
				imagecopyresampled($new_img, $img, 0, 0, $x, $y, $crop_width, $crop_height, $crop_width, $crop_height);
				imagedestroy($img);
				$img = $new_img;

				$width = $crop_width;
				$height = $crop_height;
				$changed = TRUE;
			}

			if ($output_path != NULL)
			{
				$path = explode('.', $output_path);
				array_pop($path);
				$path = implode('.', $path).'.'.$ext;
			} else
			{
				$path = explode('.', $path);
				array_pop($path);
				$path = implode('.', $path).(int)$width.'x'.(int)$height.'.'.$ext;
			}

			if ($ext == 'jpg' OR $ext == 'jpeg')
			{
				imagejpeg($img, $path, 100);
			} else if ($ext == 'png')
			 {
				imagepng($img, $path);
			} else if ($ext == 'gif')
			{
				imagegif($img, $path);
			}

			return $path;
		}

		public static function get_image_size($url, $width = 800, $height = 600)
		{
			preg_match_all('/('.self::config('thumb_prefix').')(\d+.)(x)(\d+.)(\.(jpg|jpeg|gif|png))/is', basename($url), $matches);
			$result = array('width' => $width, 'height' => $height);

			$uploads = wp_upload_dir();
			$base_path = '';

			if (isset($matches[0][0]) AND count($matches[0][0]) > 0)
			{
				if (intval($matches[2][0]) > 0 AND intval($matches[4][0]) > 0)
				{
					return array('width' => intval($matches[2][0]), 'height' => $matches[4][0]);
				}

				$thumb_path = self::config('upload_dir').'/'.basename($url);

				if (file_exists($thumb_path) AND is_file($thumb_path))
				{
					list($width, $height, $type, $attr) = getimagesize($base_path);

					return array('width' => $width, 'height' => $height);
				}

				$url = str_replace($matches[0][0], $matches[5][0], $url);
				$base_path = self::config('upload_dir').'/'.basename($url);
			} else
			{
				//$base_path = str_replace($uploads['baseurl'], $uploads['basedir'], $url); maybe?
				$base_path = self::config('upload_dir').'/'.basename($url);
			}

			if (file_exists($base_path) AND is_file($base_path))
			{
				list($width, $height, $type, $attr) = getimagesize($base_path);

				$result['width'] = $width;
				$result['height'] = $height;
			}

			return $result;
		}

		public static function find_image($url)
		{
			$uploads = wp_upload_dir();

			if (substr($url, 0, strlen($uploads['base_url'])) == $uploads['base_url'])
			{
				return $url;
			}

			$name = basename($url);
			$found = FALSE;

			$dir = opendir($uploads['base_dir']);

			// exclude ether directory here
			while ($file = readdir($dir))
			{
				if (is_file($uploads['base_dir'].'/'.$file))
				{
					if (basename($file) == $name)
					{
						copy($uploads['base_dir'].'/'.$file, self::config('upload_dir').'/'.basename($file));

						return self::config('upload_url').'/'.basename($file);
					}
				}
			}

			closedir($dir);

			return self::get_remote_image($url);
		}

		public static function get_remote_image($url)
		{
			return $url;

			// download image from local server
			$url_data = parse_url($url);

			if ($url_data['host'] != $_SERVER['HTTP_HOST'])
			{
				$remote_image = self::http_get($url);

				self::write(rtrim(self::config('upload_dir'), '/').'/'.basename($url), $remote_image, FALSE);

				return rtrim(self::config('upload_url'), '/').'/'.basename($url);
			}

			return $url;
		}

		public static function image_exists($url)
		{
			$uploads = wp_upload_dir();

			if (substr($url, 0, strlen($uploads['baseurl'])) != $uploads['baseurl'])
			{
				return FALSE;
			}

			$path = str_replace($uploads['baseurl'], $uploads['basedir'], $url);

			if ( ! file_exists($path))
			{
				return FALSE;
			}

			return TRUE;
		}

		public static function get_image_base($thumbnail_url)
		{
			$url_data = parse_url($thumbnail_url);

			if ($url_data['host'] != $_SERVER['HTTP_HOST'])
			{
				return $thumbnail_url;
			}

			$thumbnail_url = self::get_remote_image($thumbnail_url);

			preg_match_all('/('.self::config('thumb_prefix').')(\d+.)(x)(\d+.)(\.(jpg|jpeg|gif|png))/is', basename($thumbnail_url), $matches);

			if (isset($matches[0][0]) AND count($matches[0][0]) > 0)
			{
				$uploads = wp_upload_dir();
				$base_url = str_replace($matches[0][0], $matches[5][0], $thumbnail_url);
				$base_path = self::config('upload_dir').'/'.basename($base_url);

				if (file_exists($base_path))
				{
					return $base_url;
				}
			}

			return $thumbnail_url;
		}

		public static function get_image_thumbnail($base_url, $width, $height)
		{
			$url_data = parse_url($base_url);

			if ($url_data['host'] != $_SERVER['HTTP_HOST'])
			{
				return $base_url;
			}

			$base_url = self::get_remote_image($base_url);
			$uploads = wp_upload_dir();

			$image = $base_url;
			$image_path = str_replace($uploads['baseurl'], $uploads['basedir'], $image);
			$image_size = getimagesize($image_path);
			$width = intval($width);
			$height = intval($height);

			if ($width == 0 OR empty($width))
			{
				$width = intval($height * $image_size[0] / $image_size[1]);
			}

			if ($height == 0 OR empty($height))
			{
				$height = intval($width * $image_size[1] / $image_size[0]);
			}

			if ($width == $image_size[0] AND $height == $image_size[1])
			{
				return $image;
			}

			preg_match_all('/('.self::config('thumb_prefix').')(\d+.)(x)(\d+.)(\.(jpg|jpeg|gif|png))/is', basename($image), $matches);

			if (isset($matches[0][0]) AND count($matches[0][0]) > 0)
			{
				if ($width == $matches[2][0] AND $height == $matches[4][0])
				{
					return $image;
				} else
				{
					return self::get_image_thumbnail(self::get_image_base($base_url), $width, $height);
				}
			}

			$pathinfo = pathinfo($image_path);
			$ext = $pathinfo['extension'];
			$filename = basename($image_path, '.'.$ext).self::config('thumb_prefix').$width.'x'.$height.'.'.$ext;

			if (file_exists(self::config('upload_dir').'/'.$filename))
			{
				return self::config('upload_url').'/'.$filename;
			}

			if ( ! file_exists(self::config('upload_dir').'/'.basename($image_path)))
			{
				copy($image_path, self::config('upload_dir').'/'.basename($image_path));
			}

			return str_replace($uploads['basedir'], $uploads['baseurl'], self::thumbnail($image_path, $width, $height, TRUE, $width, $height, self::config('upload_dir').'/'.$filename));
		}

		public static function clean_thumbnails()
		{
			$path = self::config('upload_dir').'/';
			$files = array();

			$scan_results = array_diff(scandir($path), array('.', '..'));

			foreach ($scan_results as $result)
			{
				if (is_file($path.$result))
				{
					unlink($path.$result);
				}
			}
		}

		/**
			IMAGES END
		**/

		/**
			FIELDS BEGIN
		**/

		public static function make_field($name, $rules = array(), $data = NULL, $prefix = NULL)
		{
			if ($prefix === NULL)
			{
				$prefix = rtrim(self::config('prefix'), '_').'_';
			} else
			{
				if ($prefix != '')
				{
					$prefix = rtrim($prefix, '_').'_';
				}
			}

			if ( ! isset($rules['relation']))
			{
				$rules['relation'] = 'option';
			}

			$hidden = FALSE;

			if (substr($name, 0, 1) == '_')
			{
				$hidden = TRUE;
				$name = trim($name, '_');
			}

			$value = '';

			if ($rules['type'] != 'submit')
			{
				if ($data === NULL)
				{
					if ($rules['relation'] == 'option')
					{
						$value = get_option(($hidden ? '_' : '').$prefix.$name);
					} else if ($rules['relation'] == 'meta')
					{
						global $post;

						$value = get_post_meta($post->ID, ($hidden ? '_' : '').$prefix.$name, TRUE);
					}
				} else
				{
					if (is_array($data))
					{
						if (isset($data[($hidden ? '_' : '').$prefix.str_replace('[]', '', $name)]))
						{
							$value = $data[($hidden ? '_' : '').$prefix.str_replace('[]', '', $name)];
						} else if (isset($data[str_replace('[]', '', $name)]))
						{
							$value = $data[str_replace('[]', '', $name)];
						}
					} else
					{
						$value = $data;
					}
				}

				if ( ! empty($value))
				{
					$value = htmlspecialchars(stripslashes($value));
				}
			}

			if ( ! isset($rules['value']))
			{
				$rules['value'] = '';
			}

			if (empty($value) AND isset($rules['use_default']) AND $rules['use_default'] AND $rules['type'] != 'checkbox')
			{
				$value = htmlspecialchars(stripslashes($rules['value']));
			}

			if ($rules['type'] == 'text')
			{
				if ( ! empty($value) AND (isset($rules['class']) AND strpos($rules['class'], 'upload_') !== FALSE) AND substr($value, 0, 4) != 'http')
				{
					$value = ether::path($value, TRUE);
				}

				return '<input type="text" name="'.$prefix.$name.'"'.(isset($rules['id']) ? ' id="'.$rules['id'].'"' : '').(isset($rules['class']) ? ' class="'.$rules['class'].'"' : '').' value="'.$value.'"'.(isset($rules['style']) ? ' style="'.$rules['style'].'"' : '').' />';
			} else if ($rules['type'] == 'file')
			{
				return '<input type="file" name="'.$prefix.$name.'"'.(isset($rules['id']) ? ' id="'.$rules['id'].'"' : '').(isset($rules['class']) ? ' class="'.$rules['class'].'"' : '').' value="'.$value.'"'.(isset($rules['style']) ? ' style="'.$rules['style'].'"' : '').' />';
			} else if ($rules['type'] == 'hidden')
			{
				return '<input type="hidden" name="'.$prefix.$name.'"'.(isset($rules['id']) ? ' id="'.$rules['id'].'"' : '').(isset($rules['class']) ? ' class="'.$rules['class'].'"' : '').' value="'.$value.'"'.(isset($rules['style']) ? ' style="'.$rules['style'].'"' : '').' />';
			} else if ($rules['type'] == 'textarea')
			{
				return '<textarea name="'.$prefix.$name.'"'.(isset($rules['cols']) ? ' cols="'.$rules['cols'].'"' : '').(isset($rules['rows']) ? ' rows="'.$rules['rows'].'"' : '').(isset($rules['id']) ? ' id="'.$rules['id'].'"' : '').(isset($rules['class']) ? ' class="'.$rules['class'].'"' : '').''.(isset($rules['style']) ? ' style="'.$rules['style'].'"' : '').'>'.$value.'</textarea>';
			} else if ($rules['type'] == 'select')
			{
				$select = '<select name="'.$prefix.$name.'"'.(isset($rules['id']) ? ' id="'.$rules['id'].'"' : '').(isset($rules['class']) ? ' class="'.$rules['class'].'"' : '').' value="'.$value.'"'.(isset($rules['style']) ? ' style="'.$rules['style'].'"' : '').'>';
				$group = '';
				$last_group = '';

				if (isset($rules['options']))
				{
					foreach ($rules['options'] as $option_key => $option_value)
					{
						if (isset($option_value['group']) AND $option_value['group'] != $group)
						{
							if ($group != '')
							{
								$select .= '</optgroup>';
							}

							$group = $option_value['group'];

							$select .= '<optgroup label="'.$group.'">';
						}

						$select .= '<option value="'.$option_key.'"'.($option_key == $value ? ' selected="selected"' : '').'>'.( ! is_array($option_value) ? $option_value : $option_value['name']).'</option>';
					}
				} else
				{
					if ( ! empty($value))
					{
						$select .= '<option value="'.$value.'" selected="selected">'.$value.'</option>';
					} else
					{
						$select .= '<option></option>';
					}
				}

				if ($group != '')
				{
					$select .= '</optgroup>';
				}

				$select .= '</select>';

				return $select;
			} else if ($rules['type'] == 'checkbox')
			{
				return '<input type="checkbox" name="'.$prefix.$name.'"'.(isset($rules['id']) ? ' id="'.$rules['id'].'"' : '').(isset($rules['class']) ? ' class="'.$rules['class'].'"' : '').($value == 'on' ? ' checked="checked"' : '').''.(isset($rules['style']) ? ' style="'.$rules['style'].'"' : '').' />';
			} else if ($rules['type'] == 'submit')
			{
				$value = $rules['value'];

				return '<button type="submit" name="'.$prefix.$name.'"'.(isset($rules['id']) ? ' id="'.$rules['id'].'"' : '').' class="'.(isset($rules['class']) && ! empty($rules['class']) ? $rules['class'] : 'ether-button-border ether-button-theme-light ether-button-size-medium ').'"'.(isset($rules['style']) ? ' style="'.$rules['style'].'"' : '').' value="true">'.$rules['value'].'</button>';
			} else if ($rules['type'] == 'image')
			{
				if ( ! empty($value) AND substr($value, 0, 4) != 'http')
				{
					$value = ether::path($value, TRUE);
				}

				return '<img src="'.($value != '' ? $value : $rules['value']).'" alt="'.(isset($rules['alt']) ? $rules['alt'] : '').'"'.((isset($rules['width']) AND $rules['width'] > 0) ? ' width="'.$rules['width'].'"' : '').((isset($rules['height']) AND $rules['height'] > 0) ? ' height="'.$rules['height'].'"' : '').(isset($rules['id']) ? ' id="'.$rules['id'].'"' : '').(isset($rules['class']) ? ' class="ether-preview '.$rules['class'].'"' : '').''.(isset($rules['style']) ? ' style="'.$rules['style'].'"' : '').' />';
			} else if ($rules['type'] == 'upload_image')
			{
				return '<button type="submit" name="'.$prefix.$name.'" class="ether-button-border ether-button-theme-light ether-button-size-medium upload_image'.(isset($rules['class']) ? ' '.$rules['class'] : '').(isset($rules['width']) ? ' width-'.$rules['width'] : '').(isset($rules['height']) ? ' height-'.$rules['height'] : '').((isset($rules['single']) AND $rules['single']) ? ' single' : '').((isset($rules['tab']) AND $rules['tab']) ? ' tab-'.$rules['tab'] : ' tab-images').(isset($rules['callback']) ? ' callback-'.$rules['callback'] : '').'"'.(isset($rules['style']) ? ' style="'.$rules['style'].'"' : '').'>'.$rules['value'].'</button>';
			} else if ($rules['type'] == 'upload_media')
			{
				return '<button type="submit" name="'.$prefix.$name.'" class="ether-button-border ether-button-theme-light ether-button-size-medium upload_media"'.(isset($rules['class']) ? ' '.$rules['class'] : '').(isset($rules['style']) ? ' style="'.$rules['style'].'"' : '').'>'.$rules['value'].'</button>';
			} else if ($rules['type'] == 'color')
			{
				return '<div class="custom-admin-1"><span class="color-picker-trig" style="'.( ! empty($value) ? 'background-color: '.$value.';' : '').'"></span><div class="ether-color-picker" style="display: none; position: absolute; z-index: 50;"></div> <input type="text" name="'.$prefix.$name.'"'.(isset($rules['id']) ? ' id="'.$rules['id'].'"' : '').' class="ether-color'.(isset($rules['class']) ? ' '.$rules['class'] : '').'" value="'.$value.'"'.(isset($rules['style']) ? ' style="'.$rules['style'].'"' : '').' /></div>';
			}
		}

		public static function handle_field($data, $rules, $prefix = NULL)
		{
			if ($prefix === NULL)
			{
				$prefix = rtrim(self::config('prefix'), '_').'_';
			} else
			{
				if ($prefix != '')
				{
					$prefix = rtrim($prefix, '_').'_';
				}
			}

			foreach ($rules as $rule => $fields)
			{
				foreach ($fields as $field)
				{
					if ( ! isset($field['relation']))
					{
						$field['relation'] = 'option';
					}

					$hidden = FALSE;

					if (substr($field['name'], 0, 1) == '_')
					{
						$hidden = TRUE;
						$field['name'] = trim($field['name'], '_');
					}

					if (isset($data[($hidden ? '_' : '').$prefix.$field['name']]))
					{
						if ($field['relation'] == 'option')
						{
							$current = get_option(($hidden ? '_' : '').$prefix.$field['name']);
						} else if ($field['relation'] == 'meta')
						{
							global $post;

							$current = get_post_meta($post->ID, ($hidden ? '_' : '').$prefix.$field['name'], TRUE);
						}

						if ($data[($hidden ? '_' : '').$prefix.$field['name']] != $current)
						{
							if ($field['relation'] == 'option')
							{
								update_option(($hidden ? '_' : '').$prefix.$field['name'], $data[($hidden ? '_' : '').$prefix.$field['name']]);
							} else if ($field['relation'] == 'meta')
							{
								global $post;

								update_post_meta($post->ID, ($hidden ? '_' : '').$prefix.$field['name'], $data[($hidden ? '_' : '').$prefix.$field['name']]);
							}
						} else if (empty($data[($hidden ? '_' : '').$prefix.$field['name']]) AND empty($current))
						{
							if ($field['relation'] == 'option')
							{
								update_option(($hidden ? '_' : '').$prefix.$field['name'], $field['value']);
							} else if ($field['relation'] == 'meta')
							{
								global $post;

								update_post_meta($post->ID, ($hidden ? '_' : '').$prefix.$field['name'], $field['value']);
							}
						}
					} else
					{
						if ($field['relation'] == 'option')
						{
							update_option(($hidden ? '_' : '').$prefix.$field['name'], $field['value']);
						} else if ($field['relation'] == 'meta')
						{
							global $post;

							update_post_meta($post->ID, ($hidden ? '_' : '').$prefix.$field['name'], $field['value']);
						}
					}
				}
			}

			return FALSE;
		}

		public static function handle_field_group($data, $rules, $prefix = NULL)
		{
			if ($prefix === NULL)
			{
				$prefix = rtrim(self::config('prefix'), '_').'_';
			} else
			{
				if ($prefix != '')
				{
					$prefix = rtrim($prefix, '_').'_';
				}
			}

			foreach ($rules as $group_name => $fields)
			{
				$group_data = array();

				$relation = 'option';
				$overwrite = FALSE;

				if (isset($fields['relation']))
				{
					$relation = $fields['relation'];
					unset($fields['relation']);
				}

				if (isset($fields['overwrite']))
				{
					$overwrite = $fields['overwrite'];
					unset($fields['overwrite']);
				}

				foreach ($fields as $field)
				{
					if (isset($data[$prefix.$field['name']]))
					{
						foreach ($data[$prefix.$field['name']] as $k => $v)
						{
							if ( ! isset($group_data[$k]) OR ! is_array($group_data[$k]))
							{
								$group_data[$k] = array();
							}

							$group_data[$k][$field['name']] = $v;
						}
					} else if ( ! empty($field['value']))
					{
						if (is_array($field['value']))
						{
							foreach ($field['value'] as $v)
							{
								$group_data[][$field['name']] = $v;
							}
						} else
						{
							$group_data[0][$field['name']] = $field['value'];
						}
					}
				}

				if ($relation == 'option')
				{
					update_option($prefix.$group_name, $group_data);
				} else if ($relation == 'meta')
				{
					global $post;

					update_post_meta($post->ID, $prefix.$group_name, $group_data);
				}
			}

			return $group_data;
		}

		/**
			FIELDS END
		**/

		/**
			BACKUP BEGIN
		**/

		public static function backup_uploads($timestamp = NULL)
		{
			$uploads = wp_upload_dir();
			$uploads = $uploads['basedir'];

			$date = date('Y-m-d-h-i-s');

			if ($timestamp != NULL)
			{
				$date = $timestamp;
			}

			$path = WP_CONTENT_DIR.'/ether-backup/uploads/';

			if ( ! is_dir($path.$date))
			{
				mkdir($path.$date, 0755);
			}

			return self::copy($uploads, $path.$date);
		}

		public static function backup_files($timestamp = NULL, $to, $from, $list = array())
		{
			$target = WP_CONTENT_DIR.'/'.$from.'/';

			$date = date('Y-m-d-h-i-s');

			if ($timestamp != NULL)
			{
				$date = $timestamp;
			}

			$path = WP_CONTENT_DIR.'/ether-backup/'.$to.'/';

			if ( ! is_dir($path.$date))
			{
				mkdir($path.$date, 0755);
			}

			return self::copy($target, $path.$date, $list);
		}

		public static function backup_table($table, $timestamp = NULL, $rules = array())
		{
			$date = date('Y-m-d-h-i-s');

			if ($timestamp != NULL)
			{
				$date = $timestamp;
			}

			$path = WP_CONTENT_DIR.'/ether-backup/database/';

			if ( ! is_dir($path.$date))
			{
				mkdir($path.$date, 0755);
			}

			global $wpdb;
			global $table_prefix;
			$prefix = $table_prefix;

			if (defined('MULTISITE') AND MULTISITE AND ($table == 'usermeta' OR $table == 'users'))
			{
				$prefix = $wpdb->base_prefix;
			}

			$result = mysql_query('SELECT * FROM `'.$prefix.$table.'`');

			if (mysql_errno() != 0)
			{
				trigger_error(mysql_error());

				return FALSE;
			}

			$fields = mysql_num_fields($result);
			$field_names = array();

			$output = '';
			$line = '';

			for ($i = 0; $i < $fields; $i++)
			{
				$line .= self::$csv['enclosed'].str_replace(self::$csv['enclosed'], self::$csv['escaped'].self::$csv['enclosed'], stripslashes(mysql_field_name($result, $i))).self::$csv['enclosed'];
				$line .= self::$csv['separator'];

				$field_names[] = stripslashes(mysql_field_name($result, $i));
			}

			$output = trim(substr($line, 0, -1));
			$output .= self::$csv['terminated'];

			while ($row = mysql_fetch_array($result))
			{
				$line = '';

				for ($j = 0; $j < $fields; $j++)
				{
					if ($row[$j] == '0' OR $row[$j] != '')
					{
						if (self::$csv['enclosed'] == '')
						{
							if (isset($rules['base64']) AND in_array($field_names[$j], $rules['base64']))
							{
								$line .= base64_encode($row[$j]);
							} else
							{
								$line .= $row[$j];
							}
						} else
						{
							if (isset($rules['base64']) AND in_array($field_names[$j], $rules['base64']))
							{
								$line .= self::$csv['enclosed'].str_replace(self::$csv['enclosed'], self::$csv['escaped'].self::$csv['enclosed'], base64_encode($row[$j])).self::$csv['enclosed'];
							} else
							{
								$line .= self::$csv['enclosed'].str_replace(self::$csv['enclosed'], self::$csv['escaped'].self::$csv['enclosed'], $row[$j]).self::$csv['enclosed'];
							}
						}
					} else
					{
						$line .= '';
					}

					if ($j < $fields-1)
					{
						$line .= self::$csv['separator'];
					}
				}

				$output .= $line;
				$output .= self::$csv['terminated'];
			}

			self::write($path.$date.'/'.$table.'.csv', $output);

			return TRUE;
		}

		public static function restore_uploads($timestamp)
		{
			$uploads = wp_upload_dir();
			$uploads = $uploads['basedir'];

			if (is_dir(self::dir('backup/uploads', TRUE).'/'.$timestamp))
			{
				$path = self::dir('backup/uploads', TRUE).'/';
			} else
			{
				$path = WP_CONTENT_DIR.'/ether-backup/uploads/';
			}

			if (is_dir($path.$timestamp))
			{
				return self::copy($path.$timestamp, $uploads);
			}

			return FALSE;
		}

		public static function restore_files($timestamp, $from, $to)
		{
			$target = WP_CONTENT_DIR.'/'.$to.'/';

			if (is_dir(self::dir('backup/'.$from, TRUE).'/'.$timestamp))
			{
				$path = self::dir('backup/'.$from, TRUE).'/';
			} else
			{
				$path = WP_CONTENT_DIR.'/ether-backup/'.$from.'/';
			}

			if (is_dir($path.$timestamp))
			{
				return self::copy($path.$timestamp, $target);
			}

			return FALSE;
		}

		public static function restore_table($table, $timestamp, $rules = array())
		{
			if (is_dir(self::dir('backup/database', TRUE).'/'.$timestamp))
			{
				$path = self::dir('backup/database', TRUE).'/';
			} else
			{
				$path = WP_CONTENT_DIR.'/ether-backup/database/';
			}

			$path .= $timestamp;

			global $wpdb;
			global $table_prefix;
			$prefix = $table_prefix;

			if (defined('MULTISITE') AND MULTISITE AND ($table == 'usermeta' OR $table == 'users'))
			{
				$prefix = $wpdb->base_prefix;
			}

			$rows = array();

			if (file_exists($path.'/'.$table.'.csv'))
			{
				if (function_exists('fgetcsv'))
				{
					$csv = fopen($path.'/'.$table.'.csv', 'r');

					while (($row = fgetcsv($csv, 1000)) !== FALSE)
					{
						$rows[] = $row;
					}

					fclose($csv);
				} else
				{
					$input = self::read($path.'/'.$table.'.csv');

					if ($input[strlen($input)-1] != self::$csv['terminated'])
					{
						$input .= self::$csv['terminated'];
					}

					$length = strlen($input);
					$row = array();
					$item = 0;
					$quoted = FALSE;

					for ($i = 0; $i < $length; $i++)
					{
						$ch = $input[$i];

						if ($ch == self::$csv['enclosed'])
						{
							$quoted = ! $quoted;
						}

						if ($ch == self::$csv['terminated'] AND ! $quoted)
						{
							for ($k = 0; $k < count($row); $k++)
							{
								if ($row[$k] != '' AND $row[$k][0] == self::$csv['enclosed'])
								{
									$row[$k] = substr($row[$k], 1, strlen($row[$k]) - 2);
								}

								$row[$k] = str_replace(str_repeat(self::$csv['enclosed'], 2), self::$csv['enclosed'], $row[$k]);
							}

							$rows[] = $row;
							$row = array('');
							$item = 0;
						} else if ($ch == self::$csv['separator'] AND ! $quoted)
						{
							$row[++$item] = '';
						} else
						{
							$row[$item] .= $ch;
						}
					}
				}

				if (empty($rules))
				{
					$rules = array('clean' => TRUE, 'type' => 'insert');
				}

				if (isset($rules['clean']) AND $rules['clean'])
				{
					mysql_query('TRUNCATE TABLE `'.$prefix.$table.'`');
				}

				$fields = array_shift($rows);
				$duplicate_column_index = -1;

				if ($rules['type'] != 'insert')
				{
					array_shift($fields);

					if (isset($rules['duplicate_key']))
					{
						$field_count = count($fields);

						for ($i = 0; $i < $field_count; $i++)
						{
							if ($fields[$i] == $rules['duplicate_key'])
							{
								$duplicate_column_index = $i;
								break;
							}
						}
					}
				}

				foreach ($rows as $row)
				{
					if ($rules['type'] != 'insert')
					{
						array_shift($row);
					}

					$fields_count = count($fields);

					if ($rules['type'] == 'update')
					{
						if (isset($rules['check_callback']))
						{
							if ( ! call_user_func_array($rules['check_callback'], array($row)))
							{
								continue;
							}
						}

						$update_fields = array();
						$fields_count = count($fields);

						if (isset($rules['base64']) AND ! empty($rules['base64']))
						{
							for ($i = 0; $i < $fields_count; $i++)
							{
								if (in_array($fields[$i], $rules['base64']))
								{
									$row[$i] = base64_decode($row[$i]);
								}
							}
						}

						for ($i = 0; $i < $fields_count; $i++)
						{
							$row[$i] = self::escape_string($row[$i]);
						}

						for ($i = 0; $i < $fields_count; $i++)
						{
							$update_fields[] = '`'.$fields[$i].'`=\''.$row[$i].'\'';
						}

						if (isset($rules['duplicate_key']) AND $duplicate_column_index >= 0)
						{
							mysql_query('INSERT INTO `'.$prefix.$table.'` (`'.implode('`, `', $fields).'`) VALUES (\''.implode('\', \'', $row).'\') ON DUPLICATE KEY UPDATE `'.$rules['duplicate_key'].'`=\''.$row[$duplicate_column_index].'\'');
						} else
						{
							/* temp fix for builder */

							for ($i = 0; $i < $fields_count; $i++)
							{
								$p = self::config('prefix');

								if ($row[$i] == $p.'template_data' OR $row[$i] == $p.'template_widget_post_data')
								{
									$row[$i] = str_replace('template_', 'builder_', $row[$i]);
								}
							}

							/* temp fix for builder */

							mysql_query('INSERT INTO `'.$prefix.$table.'` (`'.implode('`, `', $fields).'`) VALUES (\''.implode('\', \'', $row).'\')');
						}
					} else
					{
						$fields_count = count($fields);

						if (isset($rules['base64']) AND ! empty($rules['base64']))
						{
							for ($i = 0; $i < $fields_count; $i++)
							{
								if (in_array($fields[$i], $rules['base64']))
								{
									$row[$i] = base64_decode($row[$i]);
								}
							}
						}

						/* temp fix for builder */

						for ($i = 0; $i < $fields_count; $i++)
						{
							$p = self::config('prefix');

							if ($row[$i] == $p.'template_data' OR $row[$i] == $p.'template_widget_post_data')
							{
								$row[$i] = str_replace('template_', 'builder_', $row[$i]);
							}
						}

						/* temp fix for builder */

						for ($i = 0; $i < $fields_count; $i++)
						{
							$row[$i] = self::escape_string($row[$i]);
						}

						mysql_query('INSERT INTO `'.$prefix.$table.'` (`'.implode('`, `', $fields).'`) VALUES (\''.implode('\', \'', $row).'\')');
					}
				}

				return TRUE;
			}

			return FALSE;
		}

		/**
			BACKUP END
		**/

		/**
			ADMIN BEGIN
		**/

		/* admin functions */

		public static function admin_panel($name, $options = array())
		{
			if ( ! self::$__init OR self::$__setup)
			{
				trigger_error('ether bad invoke');
			}

			$defaults = array('group' => self::config('name'), 'slug' => self::slug($name), 'title' => $name);

			$options = array_merge($defaults, $options);
			$group = $options['group'];

			if ($name == 'index')
			{
				return;
			}

			if ( ! isset(self::$panels[$group]) OR empty(self::$panels[$group]))
			{
				self::$panels[$group] = array();
			}

			$found = FALSE;

			foreach (self::$panels[$group] as $panel)
			{
				if ($options['slug'] == $panel['slug'])
				{
					$found = TRUE;
					break;
				}
			}

			if ( ! $found)
			{
				self::$panels[$group][] = $options;
			}
		}

		public static function admin_reset()
		{
			$groups = array_keys(self::$panels);
			$prefix = str_replace('_', '-', self::config('prefix'));

			foreach ($groups as $group)
			{
				foreach (self::$panels[$group] as $panel)
				{
					$panel = self::slug($group).'-'.self::slug($panel['slug']);
					$controller = self::config('prefix').'panel_'.str_replace('-', '_', $panel);

					if ( ! class_exists($controller))
					{
						self::import('!admin.'.$panel);
					}

					if (class_exists($controller))
					{
						call_user_func(array($controller, 'reset'));
					}
				}
			}

			foreach (self::$metaboxes as $metabox_name => $metabox_permissions)
			{
				$metabox = self::slug($metabox_name);
				$metabox_class = str_replace('-', '_', $metabox);
				$metabox_class = self::config('prefix').'metabox_'.$metabox_class;

				if ( ! class_exists($metabox_class))
				{
					self::import('!admin.metabox.'.$metabox);
				}

				if (class_exists(self::config('prefix').'metabox_'.$metabox_class))
				{
					call_user_func(array($metabox_class, 'reset'));
				}
			}
		}

		/* admin actions */

		public static function action_admin_init()
		{
			add_action('admin_menu', array('ether', 'action_admin_create'), 10);
			add_action('admin_head', array('ether', 'action_admin_header'));

			add_action('admin_print_scripts', array('ether', 'action_admin_scripts'));
			add_action('admin_print_styles', array('ether', 'action_admin_styles'));

			self::$panel = (isset($_GET['page']) ? self::clean($_GET['page']) : '');

			self::$panel = preg_replace('/'.self::slug(self::config('prefix')).'/', '', self::$panel, 1);

			self::import('!admin.'.self::$panel);
			self::$controller = self::config('prefix').'panel_'.str_replace('-', '_', self::$panel);

			if (class_exists(self::$controller))
			{
				$controller = self::$controller;
				call_user_func(array($controller, 'init'));
			}

			add_action('admin_menu', array('ether', 'action_admin_metabox_create'));
		}

		public static function action_admin_bar()
		{
			global $wp_admin_bar;

			//edit_dashboard will fail when DISALLOW_FILE_EDIT constant is set to true in wp-config
			// if ( ! current_user_can('administrator')) 
			if ( ! current_user_can('edit_dashboard')) 
			// if ( ! current_user_can('edit_dashboard')) 
			{
				return;
			}

			$admin_bar_hide_ether_menu = self::option('admin_bar_hide_ether_menu');
			if ( ! empty ($admin_bar_hide_ether_menu))
			{
				return;
			}

			$groups = array_keys(self::$panels);
			$prefix = str_replace('_', '-', self::config('prefix'));

			foreach ($groups as $group)
			{
				$parent_id = $prefix.self::slug($group);

				$wp_admin_bar->add_menu(array('id' => $parent_id, 'title' => $group, 'href' => admin_url('admin.php?page='.$parent_id)));
				//$wp_admin_bar->add_menu(array('id' => $parent_id.'-home', 'title' => $group, 'href' => admin_url('admin.php?page='.$parent_id), 'parent' => $parent_id));

				foreach (self::$panels[$group] as $panel)
				{
					$id = $prefix.self::slug($group).'-'.self::slug($panel['slug']);

					if (self::slug($group) != self::slug($panel['title']))
					{
						$wp_admin_bar->add_menu(array('id' => $id, 'title' => $panel['title'], 'href' => admin_url('admin.php?page='.$id), 'parent' => $parent_id));
					}
				}
			}
		}

		public static function action_admin_create()
		{
			$groups = array_keys(self::$panels);
			$prefix = str_replace('_', '-', self::config('prefix'));
			$allgroups = array();
			$posgroup = 30.666;

			foreach ($groups as $group)
			{
				$icon = '';

				if (self::slug($group) == 'ether')
				{
					$icon = self::path('ether/media/images/icon-empty.png', TRUE);
				}

				if (isset(self::$panels[$group][0]['icon']))
				{
					$icon = self::$panels[$group][0]['icon'];
				}

				$pos = $posgroup;

				if ( ! in_array($group, $allgroups))
				{
					add_menu_page($group, $group, 'edit_dashboard', $prefix.self::slug($group), array('ether', 'action_admin_body'), $icon, strval($pos));
					$posgroup += 0.001;
				}

				foreach (self::$panels[$group] as $panel)
				{
					if (self::slug($group) != self::slug($panel['title']))
					{
						add_submenu_page($prefix.self::slug($group), $panel['title'], $panel['title'], 'edit_dashboard', $prefix.self::slug($group).'-'.self::slug($panel['slug']), array('ether', 'action_admin_body'));
					}
				}
			}
		}

		public static function action_admin_header()
		{
			$jsconfig = '';

			foreach (self::$configjs as $k => $v)
			{
				if (is_bool($v))
				{
					$jsconfig .= ', '.$k.': '.($v ? 'true' : 'false');
				} else if (is_numeric($v))
				{
					$jsconfig .= ', '.$k.': '.$v;
				} else if (is_array($v))
				{
					$jsconfig .= ', '.$k.': '.json_encode($v);
				} else
				{
					$jsconfig .= ', '.$k.': \''.$v.'\'';
				}
			}

			echo '<script type="text/javascript">var ether = { placeholder: { img: \''.ether::path('media/images/placeholder.png', TRUE).'\', video: \''.ether::path('media/images/placeholder-video.png', TRUE).'\', empty: \''.ether::path('media/images/placeholder-empty.png', TRUE).'\'}, path: \''.self::path('/', TRUE).'\', templatepath: \''.get_template_directory_uri().'/\', stylesheetpath: \''.get_stylesheet_directory_uri().'/\', upload_preview: null, custom_insert: null, upload_callback: null, upload_caller: null, upload_target: null, $_GET: [], editor: null, prefix: \''.self::config('prefix').'\''.$jsconfig.' }; var __GET = window.location.href.slice(window.location.href.indexOf("?") + 1).split("&"); for (var i = 0; i < __GET.length; i++) { var hash = __GET[i].split("="); ether.$_GET.push(hash[0]); ether.$_GET[hash[0]] = hash[1]; }</script>';

			if (isset($_POST['save']))
			{
				if (class_exists(self::$controller))
				{
					$controller = self::$controller;
					call_user_func(array($controller, 'save'));
				}
			}

			if (isset($_POST['reset']))
			{
				if (class_exists(self::$controller))
				{
					$controller = self::$controller;
					call_user_func(array($controller, 'reset'));
				}
			}

			if (class_exists(self::$controller))
			{
				$controller = self::$controller;

				call_user_func(array($controller, 'header'));
			}
		}

		public static function action_admin_scripts()
		{
			self::script( array
			(
				array('slug' => 'jquery'),
				array('slug' => 'thickbox'),
				array('slug' => 'media-upload'),
				array('slug' => 'farbtastic'),
				array
				(
					'path' => 'ether/media/scripts/ether.js',
					'deps' => array('jquery', 'thickbox', 'media-upload', 'farbtastic'),
					'version' => self::config('version')
				)
			));

			if (self::config('debug'))
			{
				self::script( array
				(
					array('path' => 'ether/media/scripts/debug.js', 'deps' => array('jquery'), 'version' => self::config('version'))
				));
			}
		}

		public static function action_admin_styles()
		{
			self::stylesheet( array
			(
				array('slug' => 'thickbox'),
				array('slug' => 'farbtastic'),
				array
				(
					'path' => 'ether/media/stylesheets/ether.css',
					'version' => self::config('version')
				),
				array
				(
					'path' => 'ether/media/stylesheets/debug.css',
					'version' => self::config('version')
				)
			));
		}

		public static function action_admin_body()
		{
			$body = '';

			$errors = array();

			foreach (self::$deps as $type => $dependencies)
			{
				foreach ($dependencies as $dependency)
				{
					$data = $dependency['requirment'];
					$message = $dependency['error'];

					if ($message == NULL)
					{
						$message = 'DEP TEST FAILED: '.$type.' '.(is_array($data) ? implode(' ', $data) : $data);
					}

					switch ($type)
					{
						case 'class':
							if ( ! class_exists($data))
							{
								$errors[] = $message;
							}
						break;

						case 'function':
							if ( ! function_exists($data))
							{
								$errors[] = $message;
							}
						break;

						case 'directory':
							if ( ! is_dir($data))
							{
								$errors[] = $message;
							}
						break;

						case 'writable':
							if ( ! is_writable($data))
							{
								$errors[] = $message;
							}
						break;

						case 'readable':
							if ( ! is_readable($data))
							{
								$errors[] = $message;
							}
						break;

						case 'file':
							if ( ! file_exists($data))
							{
								$errors[] = $message;
							}
						break;

						case 'php':
							if (version_compare(phpversion(), $data, '<'))
							{
								$errors[] = $message;
							}
						break;

						case 'wp':
							global $wp_version;

							if (version_compare($wp_version, $data, '<'))
							{
								$errors[] = $message;
							}
						break;

						case 'php_ini':
							if (isset($data[0]) AND isset($data[1]))
							{
								if (ini_get($data[0]) != $data[1])
								{
									$errors[] = $message;
								}
							} else
							{
								$errors[] = $message;
							}
						break;
					}
				}
			}

			if (count($errors) > 0)
			{
				$body .= '<div id="message" class="error">';
			}

			foreach ($errors as $error)
			{
				$body .= '<p>'.$error.'</p>';
			}

			if (count($errors) > 0)
			{
				$body .= '</div>';
			}

			$body .= '<div class="wrap">';
			$body .= '<form method="post" class="ether-form def" enctype="multipart/form-data" encoding="multipart/form-data">';

			if (class_exists(self::$controller))
			{
				$controller = self::$controller;
				$body .= call_user_func(array($controller, 'body'));

				if (call_user_func(array($controller, 'use_controls')))
				{
					$body .= '<fieldset>
							<button type="submit" class="ether-button-border ether-button-theme-light ether-button-size-medium alignright ether-button-with-icon save-icon" name="save"><span class="ether-button-icon"></span><span>'.ether::langr('Save').'</span></button>
							<button type="submit" class="ether-button-border ether-button-theme-light ether-button-size-medium alignright ether-button-with-icon reset-icon" name="reset"><span class="ether-button-icon"></span><span>'.ether::langr('Reset').'</span></button>
					</fieldset>';
				}
			}

			$body .= '</form>';
			$body .= '</div>';

			echo $body;
		}

		/**
			ADMIN END
		**/

		/**
			ADMIN METABOX BEGIN
		**/

		/* admin metabox functions */

		public static function admin_metabox($name, $options = array())
		{
			if ( ! self::$__init OR self::$__setup)
			{
				trigger_error('ether bad invoke');
			}

			$defaults = array('permissions' => array('post', 'page'), 'context' => 'normal', 'priority' => 'high');

			if ( ! array_key_exists($name, self::$metaboxes))
			{
				self::$metaboxes[$name] = array_merge($defaults, $options);
			} else
			{
				$merged_data = array_merge($defaults, $options);

				self::$metaboxes[$name]['permissions'] = array_merge(self::$metaboxes[$name]['permissions'], $merged_data['permissions']);
				self::$metaboxes[$name]['context'] = self::$metaboxes[$name]['context'];
				self::$metaboxes[$name]['permissions'] = self::$metaboxes[$name]['permissions'];
			}
		}

		public static function admin_metabox_permission($class)
		{
			return self::$metabox_permissions[$class];
		}

		public static function admin_metabox_get($name = NULL)
		{
			if ($name != NULL)
			{
				return self::$metaboxes[$name];
			}

			return self::$metaboxes;
		}

		/* admin metabox actions */

		public static function action_admin_metabox_create()
		{
			foreach (self::$metaboxes as $metabox_name => $metabox_options)
			{
				$metabox = self::slug($metabox_name);
				$metabox_class = str_replace('-', '_', $metabox);
				$metabox_class = self::config('prefix').'metabox_'.$metabox_class;

				if ( ! class_exists($metabox_class))
				{
					self::import('!admin.metabox.'.$metabox);
				}

				if (class_exists($metabox_class))
				{
					call_user_func_array(array($metabox_class, 'set_class'), array($metabox_class));
					self::$metabox_permissions[$metabox_class] = $metabox_options['permissions'];

					call_user_func(array($metabox_class, 'init'));

					$post_types = array_merge(array('post', 'page'), array_keys(self::$custom_post_types));

					foreach ($metabox_options['permissions'] as $permission)
					{
						if (substr($permission, 0, 3) != 'id:' AND substr($permission, 0, 9) != 'template:')
						{
							add_meta_box($metabox, isset($metabox_options['title']) ? $metabox_options['title'] : $metabox_name, array($metabox_class, 'action_body'), $permission, $metabox_options['context'], $metabox_options['priority']);
						} else
						{
							foreach($post_types as $type)
							{
								add_meta_box($metabox, isset($metabox_options['title']) ? $metabox_options['title'] : $metabox_name, array($metabox_class, 'action_body'), $type, $metabox_options['context'], $metabox_options['priority']);
							}
						}
					}

					add_action('save_post', array($metabox_class, 'action_save'), 1, 2);
					add_action('admin_head', array($metabox_class, 'action_header'));

					add_action('admin_print_scripts', array($metabox_class, 'action_scripts'));
					add_action('admin_print_styles', array($metabox_class, 'action_styles'));
				}
			}
		}

		/**
			ADMIN METABOX END
		**/

		/**
			ADMIN TAX METABOX BEGIN
		*/

		/* admin tax metabox functions */

		public static function admin_tax_metabox($name, $options = array())
		{
			if ( ! self::$__init OR self::$__setup)
			{
				trigger_error('ether bad invoke');
			}

			$defaults = array('permissions' => array('category'), 'slug' => self::slug($name), 'title' => $name, 'callback' => FALSE);

			$options = array_merge($defaults, $options);

			foreach ($options['permissions'] as $type)
			{
				self::$tax_metabox[$type][$options['slug']] = $options;
			}
		}

		/**
			ADMIN TAX METABOX END
		**/

		/**
			ADMIN COLUMN BEGIN
		**/

		/* admin column functions */

		public static function admin_column($name, $options = array())
		{
			if ( ! self::$__init OR self::$__setup)
			{
				trigger_error('ether bad invoke');
			}

			$defaults = array('permissions' => array('post', 'page'),  'slug' => self::slug($name), 'title' => $name, 'order' => 99, 'meta' => self::slug($name), 'meta_default' => '', 'meta_output' => '%', 'callback' => TRUE);

			$options = array_merge($defaults, $options);

			foreach ($options['permissions'] as $type)
			{
				global $post_type;

				$ptype = $post_type;

				if (empty($ptype))
				{
					$ptype = isset($_REQUEST['post_type']) ? $_REQUEST['post_type'] : 'post';
				}

				if ($type == $ptype)
				{
					self::$columns[$type][$options['slug']] = $options;
				}
			}
		}

		/* admin column actions */

		public static function action_admin_column_meta($name)
		{
			global $post;
			global $post_type;

			if (isset(self::$columns[$post_type]) AND isset(self::$columns[$post_type][$name]))
			{
				if (self::$columns[$post_type][$name]['callback'] === TRUE AND ! empty(self::$columns[$post_type][$name]['meta']))
				{
					$value = self::meta(self::$columns[$post_type][$name]['meta'], TRUE, $post->ID);

					if (empty($value) AND ! empty(self::$columns[$post_type][$name]['meta_default']))
					{
						$value = self::$columns[$post_type][$name]['meta_default'];
					}

					echo str_replace('%', $value, self::$columns[$post_type][$name]['meta_output']);
				}
			}
		}

		/* admin column filters */

		public static function filter_admin_column_create($columns)
		{
			global $post_type;

			if (isset(self::$columns[$post_type]))
			{
				foreach (self::$columns[$post_type] as $column)
				{
					self::array_insert($columns, $column['order'], array($column['slug'] => $column['title']));
				}
			}

			return $columns;
		}

		/**
			ADMIN COLUMN END
		**/

		/**
			THEME/PLUGIN BEGIN
		**/

		/* theme/plugin actions */

		public static function action_config()
		{

		}

		public static function action_run()
		{

		}

		public static function action_activate()
		{

		}

		public static function action_deactivate()
		{

		}

		/**
			THEME/PLUGIN END
		**/
	}
}

/**
	MODULE BEGIN
**/
if ( ! class_exists('ether_module_prototype'))
{
	class ether_module_prototype
	{
		public static $class = 'ether_module';
		public static $actions = array('init', 'wp_head', 'admin_init', 'wp_print_scripts', 'wp_print_styles', 'admin_print_scripts', 'admin_print_styles', 'wp_footer', 'admin_footer');

		public static function get_class()
		{
			// prevent from using get_called_class() function defined in jigoshop plugins
			// it causes fatal error memory exhausted

			// if some plugin/theme provides his own implementaion of get_called_class() function
			// and ether will detect this function and use it as native
			// some nasty things may happen
			/*if (function_exists('get_called_class') AND ! class_exists('jigoshop_class_tools') AND ! defined('PODS_VERSION'))
			{
				return get_called_class();
			} else*/
			{
				$objects = array();
				$traces = debug_backtrace();

				foreach ($traces as $trace)
				{
					if (isset($trace['function']) AND substr($trace['function'], 0, 14) == 'call_user_func' AND isset($trace['args'][0][0]))
					{
						return $trace['args'][0][0];
					} else if (isset($trace['object']))
					{
						if (is_object($trace['object']))
						{
							$objects[] = $trace['object'];
						}
					}
				}

				if (count($objects))
				{
					return get_class($objects[0]);
				}
			}
		}

		public static function set_class($class)
		{
			self::$class = $class;
		}

		public static function __module()
		{
			foreach (self::$actions as $action)
			{
				if (substr($action, 0, 3) != 'wp_')
				{
					if (method_exists(self::$class, 'wp_'.$action))
					{
						add_action($action, array(self::$class, 'wp_'.$action), 99);
					}
				} else
				{
					if (method_exists(self::$class, $action))
					{
						add_action($action, array(self::$class, $action), 99);
					}
				}
			}
		}

		public static function init()
		{

		}

		public static function wp_init()
		{

		}

		public static function wp_head()
		{

		}

		public static function wp_admin_init()
		{

		}

		public static function wp_admin_head()
		{

		}

		public static function wp_print_scripts()
		{

		}

		public static function wp_print_styles()
		{

		}

		public static function wp_admin_print_scripts()
		{

		}

		public static function wp_admin_print_styles()
		{

		}

		public static function wp_admin_footer()
		{

		}

		public static function wp_footer()
		{

		}
	}
}

if ( ! class_exists('ether_module'))
{
	class ether_module extends ether_module_prototype {}
}

/**
	MODULE END
**/

/**
	PANEL MODULE BEGIN
**/

if ( ! class_exists('ether_panel_prototype') AND class_exists('ether_module'))
{
	class ether_panel_prototype extends ether_module
	{
		public static function init()
		{

		}

		public static function header()
		{

		}

		public static function body()
		{

		}

		public static function scripts()
		{

		}

		public static function styles()
		{

		}

		public static function save()
		{

		}

		public static function reset()
		{

		}

		public static function __module()
		{

		}

		public static function use_controls()
		{
			return TRUE;
		}
	}
}

if ( ! class_exists('ether_panel'))
{
	class ether_panel extends ether_panel_prototype {}
}

/**
	PANEL MODULE END
**/

/**
	METABOX MODULE BEGIN
**/

if ( ! class_exists('ether_metabox_prototype') AND class_exists('ether_module'))
{
	class ether_metabox_prototype extends ether_module
	{
		public static function init()
		{

		}

		public static function action_header()
		{
			$class = self::get_class();

			if (call_user_func(array($class, 'can_run')))
			{
				echo call_user_func(array($class, 'header'));
			}
		}

		public static function header()
		{

		}

		public static function action_body()
		{
			$class = self::get_class();

			if (call_user_func(array($class, 'can_run')))
			{
				echo call_user_func(array($class, 'body'));
			}
		}

		public static function body()
		{

		}

		public static function action_scripts()
		{
			$class = self::get_class();

			if (call_user_func(array($class, 'can_run')))
			{
				call_user_func(array($class, 'scripts'));
			}
		}

		public static function scripts()
		{

		}

		public static function action_styles()
		{
			$class = self::get_class();

			if (call_user_func(array($class, 'can_run')))
			{
				call_user_func(array($class, 'styles'));
			}
		}

		public static function styles()
		{

		}

		public static function reset()
		{

		}

		public static function action_save($post_id = -1)
		{
			$class = self::get_class();

			if (call_user_func(array($class, 'can_run')))
			{
				call_user_func_array(array($class, 'save'), array($post_id));
			}
		}

		public static function save($post_id)
		{

		}

		public static function can_run()
		{
			$class = self::get_class();
			$permissions = ether::admin_metabox_permission($class);

			global $post;

			$run = FALSE;

			if (isset($post))
			{
				foreach ($permissions as $permission)
				{
					if ($permission == $post->post_type)
					{
						$run = TRUE;
					} else if (substr($permission, 0, 9) == 'template:')
					{
						$template = substr($permission, 9);

						if ($template == get_post_meta($post->ID, '_wp_page_template', TRUE))
						{
							$run = TRUE;
						}
					} else if (substr($permission, 0, 3) == 'id:')
					{
						$id = substr($permission, 3);

						if ($id == $post->ID)
						{
							$run = TRUE;
						}
					}
				}
			}

			return $run;
		}

		public static function __module()
		{

		}
	}
}

if ( ! class_exists('ether_metabox'))
{
	class ether_metabox extends ether_metabox_prototype {}
}

/**
	METABOX MODULE END
**/

/**
	POST TYPE MODULE BEGIN
**/

if ( ! class_exists('ether_post_type_prototype') AND class_exists('ether_module'))
{
	class ether_post_type_prototype extends ether_module
	{
		public static function wp_admin_head()
		{
			$type = str_replace(array(ether::config('prefix'), 'post_', '_'), array('', '', '-'), self::get_class());

			global $post_type;

			if ((isset($_GET['post_type']) AND $_GET['post_type'] == $type) OR $post_type == $type)
			{
				echo '<style type="text/css"> #icon-edit { background: transparent url('.ether::path('admin/media/images/posts/'.$type.'-big.png', TRUE).') no-repeat; } </style>';
			}
		}
	}
}

if ( ! class_exists('ether_post_type'))
{
	class ether_post_type extends ether_post_type_prototype {}
}

/**
	POST TYPE MODULE END
**/


/**
	IMAGES TB
**/

if ( ! class_exists('ether_media_tab_prototype'))
{
	class ether_media_tab_prototype
	{
		protected $slug = '';
		protected $title = '';
		protected $can_upload = FALSE;
		protected $images = array();
		protected $query_images = array();

		public function __construct($slug, $title, $can_upload = FALSE)
		{
			$this->slug = $slug;
			$this->title = $title;
			$this->can_upload = $can_upload;

			add_filter('media_upload_tabs', array(&$this, 'action_build_tab'), 99);
			add_action('media_upload_'.$this->slug, array(&$this, 'action_menu_handle'));
			add_action('admin_head', array(&$this, 'action_header'));
		}

		public function action_build_tab($tabs)
		{
			$newtab[$this->slug] = $this->title;

			return array_merge($tabs, $newtab);
		}

		public function action_menu_handle()
		{
			if ($this->slug == $_GET['tab'])
			{
				return wp_iframe(array($this, 'media_process'));
			}
		}

		public function media_process()
		{
			global $wpdb;

			if ($this->slug != $_GET['tab'])
			{
				return;
			}

			media_upload_header();

			$page = 1;

			if (isset($_GET['pag']) AND $_GET['pag'] > 1)
			{
				$page = intval($_GET['pag']);
			}

			$per_page = 42;

			$this->get_images($page, $per_page);
			$count_posts = $wpdb->get_var('SELECT COUNT(*) FROM `'.$wpdb->posts.'` WHERE `post_type`=\'attachment\' AND SUBSTRING(`post_mime_type`, 1, 5)=\'image\'');

			if ($count_posts > 0)
			{
				$count_posts = number_format($count_posts);
			}

			$pages = intval(ceil($count_posts / $per_page));

			?><script type="text/javascript">
				(function($)
				{
					function show_page(page)
					{
						var per_page = 42;
						var start = page * per_page;
						var end = start + per_page;

						$("ul.images-inner-wrap li").each( function()
						{
							var $img = $(this).find("img");

							$img.attr("src", "");
							$(this).hide();
						});

						$("ul.images-inner-wrap li").slice(start, end).each( function()
						{
							var $img = $(this).find("img");
							$img.attr("src", $img.data("url"));
							$(this).show();
						});
					}

					$( function()
					{
						$("ul.images-inner-wrap li").click( function()
						{
							if ( ! single)
							{
								$(this).toggleClass("selected");
							} else
							{
								$("ul.images-inner-wrap li.selected").removeClass("selected");
								$(this).addClass("selected");
							}

							return false;
						});

						$("button[name=insert]").click( function()
						{
							var images = [];

							$("ul.images-inner-wrap li.selected a").each( function()
							{
								images.push($(this).attr("href"));
								// images.push($(this).children('img').attr("src"));
							});

							var win = window.dialogArguments || opener || parent || top;
							win.send_to_editor(images);

							return false;
						});
					});
				})(jQuery);
			</script><form id="filter">
				<div class="tablenav">
					<div class="tablenav-pages">
					<?php
						if ($pages > 1)
						{
							if ($page > 1)
							{
								echo '<a href="'.$this->get_page_link($page - 1).'" class="prev">&laquo;</a>';
							}

							if ($page > 4)
							{
								echo '<a href="'.$this->get_page_link(1).'" class="page-numbers">1</a>';
								echo '<span class="page-numbers dots">...</span>';
							}

							for ($i = $page - 3; $i < $page; $i++)
							{
								if ($i >= 1)
								{
									echo '<a href="'.$this->get_page_link($i).'" class="page-numbers">'.$i.'</a>';
								}
							}

							echo '<a href="'.$this->get_page_link($page).'" class="page-numbers current">'.$page.'</a>';

							for ($i = $page + 1; $i < $page + 4; $i++)
							{
								if ($i <= $pages)
								{
									echo '<a href="'.$this->get_page_link($i).'" class="page-numbers">'.$i.'</a>';
								}
							}

							if ($page < $pages - 3)
							{
								echo '<span class="page-numbers dots">...</span>';
								echo '<a href="'.get_pagenum_link($pages).'" class="page-numbers">'.$pages.'</a>';
							}

							if ($page < $pages)
							{
								echo '<a href="'.get_pagenum_link($page + 1).'" class="next">&raquo;</a>';
							}

						}
					?>
					</div>
				</div>
			</form>
			<form action="" method="post" id="image-form" class="media-upload-form type-form">
			<h3 class="media-title"><?php ether::lang('Choose images'); ?></h3>
			<div class="images-outer-wrap">
				<ul class="images-inner-wrap">
				<?php
					foreach ($this->images as $image)
					{
						echo '<li class="image-entry"><div class="image-item"><a href="'.$image['image'].'"><img src="'.$image['thumbnail'].'" width="79" height="79" /></a></div></li>';
					}
				?>
				</ul>
			</div>
			<fieldset class="ether-form def media-chooser-form">
				<div class="buttonset-1 aligncenter-buttons">
					<button type="submit" class="ether-button-border ether-button-theme-light ether-button-size-medium alignright ether-button ether-button" name="insert" style="width: 200px;"><?php if (isset($_GET['single']) AND $_GET['single'] == 'true') ether::lang('Insert image'); else ether::lang('Insert images'); ?></button>
				</div>
			</fieldset>
			</form>
			<?php if ($this->can_upload): ?>
			<form enctype="multipart/form-data" method="post" action="" class="media-upload-form type-form validate" id="file-form">

	<?php media_upload_form(); ?>

	<script type="text/javascript">
	jQuery(function($){
		var preloaded = $(".media-item.preloaded");
		if ( preloaded.length > 0 ) {
			preloaded.each(function(){prepareMediaItem({id:this.id.replace(/[^0-9]/g, '')},'');});
		}
		updateMediaForm();
		post_id = 0;
		shortform = 1;
	});
	</script>
	<input type="hidden" name="post_id" id="post_id" value="0" />
	<?php wp_nonce_field('media-form'); ?>
	<div id="media-items" class="hide-if-no-js"> </div>
	<?php submit_button( __( 'Save all changes' ), 'button savebutton hide-if-no-js', 'save' ); ?>
	</form><?php endif;
		}

		public function action_header()
		{
			echo '<style type="text/css">
			#media-upload #filter { width: auto;}
			html, body#media-upload { min-width: 650px !important; }

			form#image-form { width: 100%; }
			.tb-media-title { margin: 0px 0 8px; background: #F6F6F6; color: #111; padding: 8px 8px; font-weight: normal;}



			.images-inner-wrap { margin: 0; padding: 0; }
			ul.images-inner-wrap:after { display: block; visibility: hidden; content: "."; height: 0; line-height: 0; font-size: 0; clear: both; }

			ul.images-inner-wrap .image-entry { float: left; position: relative; list-style: none; margin: 2px;}

			.images-inner-wrap .image-item { float: left; padding: 4px; margin: 0px; background-color: #ccc; overflow:hidden;}
			.images-inner-wrap .image-item:hover,
			.images-inner-wrap .selected .image-item { background-color: #333; }

			.images-inner-wrap .image-item img { display: block; }
			ul.images-inner-wrap 	.image-item img { width:80px; height: 80px; margin: 0; padding: 0;}
			table.images-inner-wrap .image-item img { width:80px; height: 80px; }




			p.ml-submit input.insert { display: block !important; }

			.media-upload-form fieldset.ether-form { padding-top: 5px; }

			fieldset.ether-form { width: 100%; padding: 0; margin: 0; border: none; }

			.tablenav { text-align: center;}
			.tablenav .tablenav-pages { float: none; display: inline-block;}
			.tablenav-pages { display: block; padding: 8px; text-align: center;}
			.tablenav-pages a { text-align: center; display: inline-block;padding: 8px 12px !important; line-height: 1.1em; border: none; color: #111;  overflow: hidden; cursor: pointer; outline: none; position: relative; text-decoration: none; color: #49A5D1; background: #f6f6f6;}
			.tablenav-pages a.current { background-color: #d6d6d6;}
			.tablenav-pages a.prev { margin-right: 4px;}
			.tablenav-pages a.next { margin-left: 4px;}
			.tablenav-pages a:hover,
			.tablenav-pages a:focus {background-color: #eee; color: #49A5D1; }
			.tablenav-pages a:active { }
			.tablenav-pages a.disabled { color: #999 !important; background: #f6f6f6 !important; }

			fieldset.upload .buttonset-1 { margin: 0; display: block;}
			.upload-image { visibility: hidden; position: absolute; top: 0; left: 0; width: 0; height: 0; }

			p.error { text-align: center; font-weight: bold; color: #C90000; }

			.ether-form.media-chooser-form { max-width: 100%;}

			</style>
			<script type="text/javascript">
				var single = '.((isset($_GET['single']) AND $_GET['single'] == 'true') ? 'true' : 'false').';
			</script>';
		}

		protected function get_images($page, $per_page)
		{
			$query_images = new WP_Query( array
			(
				'post_type' => 'attachment',
				'post_mime_type' => 'image',
				'post_status' => 'inherit',
				'posts_per_page' => $per_page,
				'offset' => ($page - 1) * $per_page
			));

			foreach ($query_images->posts as $image)
			{
				$this->images[] = array('image' => wp_get_attachment_url($image->ID), 'thumbnail' => wp_get_attachment_thumb_url($image->ID));
			}
		}

		protected function get_page_link($page)
		{
			$data = $_GET;

			$data['pag'] = $page;
			$url = 'media-upload.php?';

			foreach ($data as $k => $v)
			{
				$url .= '&'.$k.'='.$v;
			}

			return $url;
		}
	}
}

if ( ! class_exists('ether_images_tab'))
{
	class ether_images_tab extends ether_media_tab_prototype {}

	new ether_images_tab('images', __('Images'), TRUE);
}

/**
	IMAGES TAB END
**/

?>
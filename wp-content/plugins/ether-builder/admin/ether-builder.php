<?php

if ( ! class_exists('ether_panel_ether_builder'))
{
	class ether_panel_ether_builder extends ether_panel
	{
		public static function init()
		{

		}

		public static function header()
		{
			ether::stylesheet( array
			(
				array
				(
					'path' => 'admin/media/stylesheets/builder.css',
					'version' => ether::config('version')
				)
			));

			ether::script( array
			(
				array
				(
					'slug' => 'builder',
					'path' => 'admin/media/scripts/builder.js',
					'deps' => array('jquery', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable'),
					'version' => ether::config('version')
				),
				array
				(
					'slug' => 'builder-admin',
					'path' => 'admin/media/scripts/admin.js',
					'deps' => array('builder'),
					'version' => ether::config('version')
				)
			));
		}

		public static function reset()
		{
			ether::handle_field(array(), array
			(
				'input' => array
				(
					array
					(
						'name' => 'builder_grid_spacing',
						'value' => 30
					)
				),

				'checkbox' => array
				(
					array
					(
						'name' => 'builder_tab_default',
						'value' => ''
					),
					array
					(
						'name' => 'hide_visual_tab',
						'value' => ''
					),
					array
					(
						'name' => 'hide_html_tab',
						'value' => ''
					),
					array
					(
						'name' => 'builder_archive_hide',
						'value' => ''
					),
					array
					(
						'name' => 'builder_color',
						'value' => ''
					),
					array
					(
						'name' => 'builder_style',
						'value' => ''
					),
					array
					(
						'name' => 'builder_revisions',
						'value' => ''
					),
					array
					(
						'name' => 'builder_builder2wp_disable',
						'value' => ''
					),
					array
					(
						'name' => 'builder_wp2builder_enable',
						'value' => ''
					)
				)
			));

			ether::handle_field_group(array(), array
			(
				'builder_hidden_types' => array()
			));

			ether::handle_field_group(array(), array
			(
				'builder_hidden_widgets' => array()
			));
		}

		public static function save()
		{
			ether::handle_field($_POST, array
			(
				'input' => array
				(
					array
					(
						'name' => 'builder_grid_spacing',
						'value' => 30
					)
				),

				'checkbox' => array
				(
					array
					(
						'name' => 'builder_tab_default',
						'value' => ''
					),
					array
					(
						'name' => 'hide_visual_tab',
						'value' => ''
					),
					array
					(
						'name' => 'hide_html_tab',
						'value' => ''
					),
					array
					(
						'name' => 'builder_archive_hide',
						'value' => ''
					),
					array
					(
						'name' => 'builder_color',
						'value' => ''
					),
					array
					(
						'name' => 'builder_style',
						'value' => ''
					),
					array
					(
						'name' => 'builder_revisions',
						'value' => ''
					),
					array
					(
						'name' => 'builder_builder2wp_disable',
						'value' => ''
					),
					array
					(
						'name' => 'builder_wp2builder_enable',
						'value' => ''
					)
				)
			));

			$regular_types = array('post', 'page');
			$custom_types = array_keys(get_post_types(array('_builtin' => FALSE, 'public' => TRUE, 'show_ui' => TRUE)));
			$builder_post_types = array_unique(array_merge($regular_types, $custom_types));

			$post_type_fields = array();

			foreach ($builder_post_types as $type)
			{
				$post_type_fields[] = array
				(
					'name' => ''.$type.'_post_type',
					'value' => ''
				);
			}

			ether::handle_field_group($_POST, array
			(
				'builder_hidden_types' => array_merge(array('relation' => 'option'), $post_type_fields)
			));

			ether::option('builder_hidden_types', self::get_reversed('builder_hidden_types', $post_type_fields));

			$widget_fields = array();
			$builder_widget_list = ether_builder::get_widgets();
			
			foreach ($builder_widget_list as $widget)
			{
				$slug = $widget->get_slug();

				$widget_fields[] = array
				(
					'name' => ''.$slug,
					'value' => ''
				);
			}

			ether::handle_field_group($_POST, array
			(
				'builder_hidden_widgets' => $widget_fields
			));

			ether::option('builder_hidden_widgets', self::get_reversed('builder_hidden_widgets', $widget_fields));
		}

		public static function get_reversed ($option, $map)
		{
			$reversed = array();

			$data = ether::option($option);

			if (isset($data[0]) AND ! empty($data[0]))
			{
				$data = $data[0];
			}

			foreach ($map as $elem)
			{
				$name = $elem['name'];

				if ( ! isset($data[$name]))
				{
					$reversed[$name] = 'on';
				}
			}

			$data = ether::option($option);

			if (isset($data[0]) AND ! empty($data[0]))
			{
				$data[0] = $reversed;
			} else
			{
				$data = $reversed;
			}

			return $data;
		}

		public static function body()
		{
			$regular_types = array('post', 'page');
			$custom_types = array_keys(get_post_types(array('_builtin' => FALSE, 'public' => TRUE, 'show_ui' => TRUE)));
			$builder_post_types = array_unique(array_merge($regular_types, $custom_types));
			$builder_hidden_types = ether::option('builder_hidden_types');
			$colors = apply_filters('ether_builder_presets', array('light' => array('name' => ether::langr('Light')), 'dark' => array('name' => ether::langr('Dark'))));

			if (isset($builder_hidden_types[0]) AND ! empty($builder_hidden_types[0]))
			{
				$builder_hidden_types = $builder_hidden_types[0];
			}

			$post_type_fields = '<button type="button" class="ether-button-classic ether-button-size-small alignright ether-toggle-checkbox-button ether-toggle-checkbox-button-name-post-types ether-toggle-checkbox-action-off" name="hide-all">'.ether::langr('Hide All').'</button>';
			$post_type_fields .= '<div class="ether-toggle-checkbox-group ether-toggle-checkbox-group-name-post-types">';

			foreach ($builder_post_types as $type)
			{
				strpos($type, '_post_type') === FALSE ? $type = $type.'_post_type' : '';

				$is_hidden_type = isset($builder_hidden_types[$type]);
				$checkbox_val = ( ! $is_hidden_type ? 'on' : '');
				$label = str_replace('_post_type', '', $type);

				$post_type_fields .= '<label class="ether-toggle-checkbox '.($is_hidden_type ? 'ether-toggle-checkbox-state-off' : '').'">'.ether::make_field(''.$type.'[]', array('type' => 'checkbox', 'relation' => 'custom'), $checkbox_val).' '.ether::langr("%s", $label).'</label>';
			}

			$post_type_fields .= '</div>';

			$widgets_data = ether_builder::get_widgets(TRUE);
			$builder_hidden_widgets = ether::option('builder_hidden_widgets');

			if (isset($builder_hidden_widgets[0]) AND ! empty($builder_hidden_widgets[0]))
			{
				$builder_hidden_widgets = $builder_hidden_widgets[0];
			}

			$widget_fields_output = '';
			
			foreach ($widgets_data as $key => $data)
			{
				if (count($data['widgets']))
				{
					$widget_fields_output .= '
					<div class="ether-toggle-checkbox-group-wrap">
						<h4 class="ether-toggle-checkbox-group-title form-title "alignleft">'.$data['name'].' '.(isset($data['tooltip']) ? '<small class="ether-tooltip-content-raw">'.$data['tooltip'].'</small>' : '').'</h4>
						<button type="button" class="ether-button-classic ether-button-size-small alignright ether-toggle-checkbox-button ether-toggle-checkbox-button-name-'.$key.' ether-toggle-checkbox-action-off">'.ether::langr('Hide All').'</button>
						<div class="ether-toggle-checkbox-group ether-toggle-checkbox-group-name-'.$key.'">';

					foreach ($data['widgets'] as $widget)
					{
						$is_hidden_widget = isset($builder_hidden_widgets[$widget->get_slug()]);
						$checkbox_val = ( ! $is_hidden_widget ? 'on' : '');

						$widget_fields_output .= '
								<div class="ether-toggle-checkbox-wrap">
									<label class="ether-toggle-checkbox '.($is_hidden_widget ? 'ether-toggle-checkbox-state-off' : '').'"><div class="builder-widget-icon builder-widget-icon-'.$widget->get_slug().'"></div>'.ether::make_field(''.$widget->get_slug().'[]', array('type' => 'checkbox', 'relation' => 'custom'), $checkbox_val).' '.ether::langr("%s", $widget->get_title()).'<small class="ether-tooltip-content-raw">'.($widget->get_label()).'</small></label>
								</div>';
					}

					$widget_fields_output .= '
						</div>
					</div>';
				}
			}

			$docs_url = ether::config('online_docs');
			$support_url = ether::config('online_support');

			return '<fieldset class="ether-form def">
				<div class="ether-box ether-box-no-inside">
					<h2 class="ether-box-title">'.ether::langr('Ether Content Builder Settings').'</h2>
				</div>
				<div class="ether-box">
					<h3 class="ether-box-title">'.ether::langr('Info').'</h3>
					<div class="ether-box-inside">'
						.(isset($docs_url) ? '<a class="ether-button-border ether-button-theme-light ether-button-with-icon ether-button-size-medium docs-icon" href="'.$docs_url.'"><span class="ether-button-icon"></span><span>Documentation</span></a> ' : '').
						(isset($support_url) ? '<a class="ether-button-border ether-button-theme-light ether-button-with-icon ether-button-size-medium support-icon" href="'.$support_url.'"><span class="ether-button-icon"></span><span>Support</span></a> ' : '').
					'</div>
				</div>
				<div class="ether-box">
					<h3 class="ether-box-title">'.ether::langr('General').'</h3>
					<div class="ether-box-inside">
						<h4 class="form-title">'.ether::langr('Builder Tab Visibility').'</h4>
						<label class="ether-toggle-checkbox ether-toggle-checkbox-auto">'.ether::make_field('builder_tab_default', array('type' => 'checkbox', 'relation' => 'option')).' '.ether::langr('Show Builder tab by default').'</label>
						
						<hr class="ether-divider">

						<h4 class="form-title">'.ether::langr('Editor Tabs Visibility').'</h4>
						<div class="inline-labels">
							<label class="ether-toggle-checkbox ether-toggle-checkbox-auto">'.ether::make_field('hide_visual_tab', array('type' => 'checkbox', 'relation' => 'option')).' '.ether::langr('Hide "Visual" tab').'</label>
							<label class="ether-toggle-checkbox ether-toggle-checkbox-auto">'.ether::make_field('hide_html_tab', array('type' => 'checkbox', 'relation' => 'option')).' '.ether::langr('Hide "HTML" tab').'</label>
						</div>
						<hr class="ether-divider">
						
						<h4 class="form-title">'.ether::langr('Revisions').'</h4>
						<label class="ether-toggle-checkbox ether-toggle-checkbox-auto">'.ether::make_field('builder_revisions', array('type' => 'checkbox', 'relation' => 'option')).' '.ether::langr('Enable revision support for Builder content').'</label>
						<hr class="ether-divider">

						<h4 class="form-title">'.ether::langr('Sidebars & 3rd Party Plugins/Widgets').'</h4>
						<label class="ether-toggle-checkbox ether-toggle-checkbox-auto">'.ether::make_field('builder_wp2builder_enable', array('type' => 'checkbox', 'relation' => 'option')).' '.ether::langr('Enable widgets from sidebars/external plugins in the Ether Builder').'</label>
						<label class="ether-toggle-checkbox ether-toggle-checkbox-auto">'.ether::make_field('builder_builder2wp_disable', array('type' => 'checkbox', 'relation' => 'option')).' '.ether::langr('Disable widgets from Ether Builder in the sidebars').'</label>
						<hr class="ether-divider">

						<h4 class="form-title">'.ether::langr('Misc').'</h4>
						<label class="ether-toggle-checkbox ether-toggle-checkbox-auto">'.ether::make_field('builder_archive_hide', array('type' => 'checkbox', 'relation' => 'option')).' '.ether::langr('Hide Builder content on archive/listing pages').'</label>
					</div>
				</div>


				<div class="ether-box">
					<h3 class="ether-box-title">'.ether::langr('Widget Defaults').'</h3>
					<div class="ether-box-inside">
						<h4 class="form-title">'.ether::langr('Gridsliders').'</h4>
						<label>
							<span class="label-title">'.ether::langr('Spacing').'</span> '
							.ether::make_field('builder_grid_spacing', array('type' => 'text', 'relation' => 'option', 'use_default' => true, 'value' => 30)).
							'<small>'.ether::langr('Default cell spacing for widgets featuring grid functionality').'</small>
						</label>
					</div>
				</div>


				<div class="ether-box">
					<h3 class="ether-box-title">'.ether::langr('Styles').'</h3>
					<div class="ether-box-inside">
						<label><span class="label-title">'.ether::langr('Color scheme').'</span> '.ether::make_field('builder_color', array('type' => 'select', 'relation' => 'option', 'options' => $colors)).'</label>
					</div>
				</div>

				<div id="post-types-visibility" class="ether-box">
					<h3 class="ether-box-title">'.ether::langr('Post Types Visibility').'</h3>
					<div class="ether-box-inside">
						'.$post_type_fields.'
					</div>
				</div>

				<div id="builder-widgets-visibility" class="ether-box">
					<h3 class="ether-box-title">'.ether::langr('Widgets Visibility').'</h3>
					<div class="ether-box-inside">
						<button type="button" class="ether-button-classic alignleft ether-toggle-checkbox-button ether-toggle-checkbox-button-name-all-widgets ether-toggle-checkbox-action-off">'.ether::langr('Hide All').'</button>
						<div class="ether-toggle-checkbox-group ether-toggle-checkbox-group-name-all-widgets">
							'.$widget_fields_output.'
						</div>
					</div>
				</div>

				<div class="ether-box">
					<h3 class="ether-box-title">'.ether::langr('Custom styles').'</h3>
					<div class="ether-box-inside">
						<label><span class="label-title">'.ether::langr('Custom CSS').'</span> '.ether::make_field('builder_style', array('type' => 'textarea', 'rows' => '10')).'</label>
					</div>
				</div>

				<div class="ether-box">
					<h3 class="ether-box-title">'.ether::langr('Import / Export').'</h3>
					<div class="ether-box-inside">
					</div>
				</div>
			</fieldset>';
		}
	}
}

?>
<?php

if ( ! class_exists('ether_metabox_builder'))
{
	class ether_metabox_builder extends ether_metabox
	{
		public static function init()
		{
			if (is_admin())
			{
				ether::configjs('hide_visual_tab', ether::option('hide_visual_tab') == 'on');
				ether::configjs('hide_html_tab', ether::option('hide_html_tab') == 'on');
			}
		}

		public static function header()
		{
			if (is_admin())
			{
				$screen = get_current_screen();

				if ( ! empty($screen->post_type) OR $screen->id == 'widgets')
				{
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
							'slug' => 'builder-widgets',
							'path' => 'admin/media/scripts/widgets.js',
							'deps' => array('builder'),
							'version' => ether::config('version')
						),
						array
						(
							'slug' => 'builder-live-previews',
							'path' => 'admin/media/scripts/live-previews.js',
							'deps' => array('builder'),
							'version' => ether::config('version')
						),
						array
						(
							'slug' => 'builder-admin',
							'path' => 'admin/media/scripts/admin.js',
							'deps' => array('builder', 'builder-widgets', 'builder-live-previews'),
							'version' => ether::config('version')
						),
					));

					$color = ether::option('builder_color');
					empty($color) ? $color = 'light' : '';

					ether::stylesheet( array
					(
						array
						(
							'path' => 'admin/media/stylesheets/builder.css',
							'version' => ether::config('version')
						),
						array
						(
							'path' => 'admin/media/stylesheets/ether-builder-preview-'.$color.'.css',
							'version' => ether::config('version')
						)
					));
				}
			}
		}

		public static function restore_revision($post_id, $revision_id)
		{
			if (ether::option('builder_revisions') == 'on')
			{
				$post = get_post($post_id);
				$revision = get_post($revision_id);
				$data = get_metadata('post', $revision->ID, ether::config('prefix').'builder_data', TRUE);

				if ($data !== FALSE)
				{
					update_post_meta($post_id, ether::config('prefix').'builder_data', $data);
				} else
				{
					delete_post_meta($post_id, ether::config('prefix').'builder_data');
				}
			}
		}

		public static function save($post_id)
		{
			global $post;
			global $post_type;

			if (isset($_POST['ether_builder_widget']['__LOCATION__']))
			{
				unset($_POST['ether_builder_widget']['__LOCATION__']);
			}

			if (isset($_POST['ether_builder_widget']['__ID__']))
			{
				unset($_POST['ether_builder_widget']['__ID__']);
			}

			if (ether::option('builder_revisions') == 'on')
			{
				$parent_id = wp_is_post_revision($post_id);

				if ($parent_id)
				{
					$parent = get_post($parent_id);

					$data = get_post_meta($parent->ID, ether::config('prefix').'builder_data', TRUE);

					if ($data !== FALSE)
					{
						add_metadata('post', $post_id, ether::config('prefix').'builder_data', $_POST['ether_builder_widget']);
					}
				} else
				{
					ether::meta('builder_data', $_POST['ether_builder_widget'], $post->ID, TRUE);
				}
			} else
			{
				if ($post != NULL)
				{
						ether::meta('builder_data', $_POST['ether_builder_widget'], $post->ID, TRUE);
				}
			}

			ether::handle_field($_POST, array
			(
				'checkbox' => array
				(
					array
					(
						'name' => 'editor_tab',
						'relation' => 'meta',
						'value' => ''
					)
				)
			));
		}

		public static function get_prototypes()
		{
			$builder_hidden_widgets = ether::option('builder_hidden_widgets');

			if (isset($builder_hidden_widgets[0]) AND ! empty($builder_hidden_widgets[0]))
			{
				$builder_hidden_widgets = $builder_hidden_widgets[0];
			}

			if ( ! is_array($builder_hidden_widgets))
			{
				$builder_hidden_widgets = array();
			}

			$widgets_data = ether_builder::get_widgets(TRUE);

			$widgets_output = '';

			foreach ($widgets_data as $key => $data)
			{
				if (count($data['widgets']))
				{
					$visible_count = 0;

					foreach ($data['widgets'] as $widget)
					{
						if ( ! isset($builder_hidden_widgets[$widget->get_slug()]))
						{
							$visible_count ++;
						}
					}

					if ($visible_count > 0)
					{
						$widgets_output .= '<h3 class="builder-widgets-group-title"><div class="text">'.$data['name'].(isset($data['tooltip']) ? '<small class="ether-tooltip-content-raw">'.$data['tooltip'].'</small>' : ' ').'</div></h3>';
						$widgets_output .= '<div class="builder-widgets-group-content builder-widgets-group-name-'.$key.'">';

						foreach ($data['widgets'] as $widget)
						{
							if (isset($builder_hidden_widgets[$widget->get_slug()]) AND $builder_hidden_widgets[$widget->get_slug()] == 'on')
							{
								$widget->hide();
							} else
							{
								$widgets_output .= $widget->admin_form();
							}
						}

						$widgets_output .= '</div>';
					}
				}
			}

			$body = '<div id="builder-widgets" style="display: none;">
				<div id="builder-widgets-top-bar">
					<button id="builder-widgets-close" name="builder-metabox-close" class="builder-metabox-close">'.ether::langr('close').'</button>
					<fieldset class="ether-form def filter-builder-widgets">
						<label class="hidden-widgets">
							<div class="hidden-widgets-count">'.ether::langr('%s hidden widgets', count($builder_hidden_widgets)).' </div>
							<div><a href="admin.php?page=ether-ether-builder" target="_blank">'.ether::langr('Manage visible widgets').'</a></div>
						</label>
						<label class="filter">
							<input type="text" placeholder="'.ether::langr('Filter widgets').'" name="builder-widget-filter" value="" />
							<div class="clear-filter dashicons dashicons-no"></div>
						</label>
					</fieldset>
				</div>
				<div class="builder-widgets-wrap">
				'.$widgets_output.'
				</div>
			</div>';

			return $body;
		}

		public static function get_previews()
		{
			$col_widgets_separator = FALSE;
			$previews_output = '';

			$previews_output .= '<div id="builder-previews">';
			
			$previews_output .= '
			<div id="builder-slider-preview" class="builder-preview" style="display:none">
				<div class="builder-preview-title">'.ether::langr('Slider Nav Preview').'</div>
				<div id="builder-slider-preview-theme">
					<div id="builder-slider-preview-wrap" class="ether-ctrl-style-0">
						<div id="builder-slider-preview-arrows" class="ether-ctrl-car">
							<div id="builder-slider-preview-arrow-left" class="ether-ctrl ether-ctrl-prev builder-slider-preview-arrow"></div>
							<div id="builder-slider-preview-arrow-right" class="ether-ctrl ether-ctrl-next builder-slider-preview-arrow"></div>
						</div>

						<div id="builder-slider-preview-pag" class="ether-ctrl-pag">
							<div class="ether-ctrl builder-slider-preview-pag-item"></div>
							<div class="ether-ctrl builder-slider-preview-pag-item"></div>
							<div class="ether-ctrl builder-slider-preview-pag-item"></div>
							<div class="ether-ctrl builder-slider-preview-pag-item"></div>
							<div class="ether-ctrl builder-slider-preview-pag-item"></div>
						</div>
					</div>
				</div>
			</div>';

			$dashicons_list = array(
				'menu', 'admin-site', 'dashboard', 'admin-post', 'admin-media', 'admin-links', 'admin-page', 'admin-comments', 'admin-appearance', 'admin-plugins', 'admin-users', 'admin-tools', 'admin-settings', 'admin-network', 'admin-home', 'admin-generic', 'admin-collapse', 'welcome-write-blog', 'welcome-add-page', 'welcome-view-site', 'welcome-widgets-menus', 'welcome-comments', 'welcome-learn-more', 'format-aside', 'format-image', 'format-gallery', 'format-video', 'format-status', 'format-quote', 'format-chat', 'format-audio', 'camera', 'images-alt', 'images-alt2', 'video-alt', 'video-alt2', 'video-alt3', 'media-archive', 'media-audio', 'media-code', 'media-default', 'media-document', 'media-interactive', 'media-spreadsheet', 'media-text', 'media-video', 'playlist-audio', 'playlist-video', 'image-crop', 'image-rotate-left', 'image-rotate-right', 'image-flip-vertical', 'image-flip-horizontal', 'undo', 'redo', 'editor-bold', 'editor-italic', 'editor-ul', 'editor-ol', 'editor-quote', 'editor-alignleft', 'editor-aligncenter', 'editor-alignright', 'editor-insertmore', 'editor-spellcheck', 'editor-expand', 'editor-contract', 'editor-kitchensink', 'editor-underline', 'editor-justify', 'editor-textcolor', 'editor-paste-word', 'editor-paste-text', 'editor-removeformatting', 'editor-video', 'editor-customchar', 'editor-outdent', 'editor-indent', 'editor-help', 'editor-strikethrough', 'editor-unlink', 'editor-rtl', 'editor-break', 'editor-code', 'editor-paragraph', 'align-left', 'align-right', 'align-center', 'align-none', 'lock', 'calendar', 'visibility', 'post-status', 'edit', 'trash', 'external', 'arrow-up', 'arrow-down', 'arrow-right', 'arrow-left', 'arrow-up-alt', 'arrow-down-alt', 'arrow-right-alt', 'arrow-left-alt', 'arrow-up-alt2', 'arrow-down-alt2', 'arrow-right-alt2', 'arrow-left-alt2', 'sort', 'leftright', 'randomize', 'list-view', 'exerpt-view', 'share', 'share-alt', 'share-alt2', 'twitter', 'rss', 'email', 'email-alt', 'facebook', 'facebook-alt', 'googleplus', 'networking', 'hammer', 'art', 'migrate', 'performance', 'universal-access', 'universal-access-alt', 'tickets', 'nametag', 'clipboard', 'heart', 'megaphone', 'schedule', 'wordpress', 'wordpress-alt', 'pressthis', 'update', 'screenoptions', 'info', 'cart', 'feedback', 'cloud', 'translation', 'tag', 'category', 'archive', 'tagcloud', 'text', 'yes', 'no', 'no-alt', 'plus', 'plus-alt', 'minus', 'dismiss', 'marker', 'star-filled', 'star-half', 'star-empty', 'flag', 'location', 'location-alt', 'vault', 'shield', 'shield-alt', 'sos', 'search', 'slides', 'analytics', 'chart-pie', 'chart-bar', 'chart-line', 'chart-area', 'groups', 'businessman', 'id', 'id-alt', 'products', 'awards', 'forms', 'testimonial', 'portfolio', 'book', 'book-alt', 'download', 'upload', 'backup', 'clock', 'lightbulb', 'microphone', 'desktop', 'tablet', 'smartphone', 'smiley'
			);

			$list_icons_list = array
			(
				'info-1', 'question-1', 'bubble-1', 'red-lock-1', 'blue-lock-1', 'check-1', 'check-2', 'check-3', 'check-4', 'check-5', 'error-1', 'error-3', 'error-4', 'error-5', 'warning-1', 'warning-2', 'warning-3', 'warning-4', 'warning-5', 'warning-6', 'arrow-1', 'arrow-2', 'arrow-3', 'arrow-4', 'arrow-5', 'download', 'download-2', 'download-3', 'download-4'
			);

			$tabs_output = '';
			$tabs_content_output = '';
						
			$dashicons_output = '
			<div id="builder-dashicons" class="builder-icon-chooser-tab-content builder-icons-wrap">
				<div class="builder-icon-chooser-no-icon"></div>
			';
			foreach($dashicons_list as $dashicon)
			{
				$dashicons_output .= '<div class="dashicons dashicons-'.$dashicon.'"></div> ';
			}

			$dashicons_output .= '
			</div>';

			$tabs_output .= '<div class="builder-icon-chooser-tab builder-icon-chooser-tab-dashicons current">'.ether::langr('Dashicons').'</div>';


			$list_icons_output = '
			<div id="ether-list-icons" class="builder-icon-chooser-tab-content builder-icons-wrap">
				<div class="builder-icon-chooser-no-icon"></div>
			';
			foreach($list_icons_list as $icon)
			{
				$list_icons_output .= '<div class="ether-list-icon ether-list-icon-'.$icon.'"></div> ';
			}

			$list_icons_output .= '
			</div>';

			$tabs_output .= '<div class="builder-icon-chooser-tab builder-icon-chooser-tab-list-icons">'.ether::langr('List Icons').'</div>';

			$tabs_content_output = $dashicons_output.$list_icons_output;

			$previews_output .= '
			<div id="builder-icon-chooser" style="display: none">
				<div class="builder-icon-chooser-top-bar">
					<div id="builder-icon-chooser-close">'.ether::langr('close').'</div>
					<div id="builder-icon-chooser-reset">'.ether::langr('reset').'</div>
				</div>
				
				<div id="builder-icon-chooser-wrap">
					<div class="builder-icon-chooser-tabs">
						'.$tabs_output.'
					</div>
					<div class="builder-icon-chooser-tabs-content">
						'.$tabs_content_output.'
					</div>
				</div>
			</div>';

			$builder_hidden_widgets = ether::option('builder_hidden_widgets');

			if ( ! is_array($builder_hidden_widgets))
			{
				$builder_hidden_widgets = array();
			}

			$widgets = ether_builder::get_widgets();

			foreach ($widgets as $widget)
			{
				if ( ! isset($builder_hidden_widgets[$widget->get_slug()]) OR $builder_hidden_widgets[$widget->get_slug()] != 'on')
				{
					$previews_output .= $widget->admin_preview();
				}
			}

			$previews_output .= '</div>';

			$body = $previews_output;

			return $body;
		}

		public static function body($builder_data = array(), $parent_id = NULL, $read_only = FALSE)
		{
			global $post;
			global $post_type;
			global $_D;

			$body = '';

			//if (($post->post_type == 'section' OR $post->post_type == 'portfolio') OR $force_output)
			{
				$thumb_size = '-'.get_option('thumbnail_size_w').'x'.get_option('thumbnail_size_h');
			    $body .= '<div id="builder-thumb-size" class="'.$thumb_size.'" style="display:none;"></div>';

				$body .= '<div id="builder-location-wrapper" class="builder-location-wrapper'.($read_only ? ' read-only' : '').'"><fieldset class="ether-form def">';//.($post_type == 'portfolio' ? '<p class="hint">'.ether::langr('The following layout will be applied to all projects that belong to this portfolio. Make sure gallery widget is included or else you won\'t be able to add images to those projects.').'</p>' : '');

				$widgets_output = '';
				$widgets = ether_builder::get_widgets();
				$locations = ether_builder::get_locations();
				$builder_widgets = array();

				if ( ! is_array($builder_data))
				{
					$builder_data = array();
				}

				$tmp_post = NULL;

				if (ether::option('builder_revisions') != 'on')
				{
					if (isset($_GET['post']) AND $_GET['post'] != $post->ID)
					{
						$tmp_post = $post;

						$post = get_post($_GET['post']);
					}
				}

				$id = $post->ID;

				if ($parent_id != NULL AND $parent_id > 0)
				{
					$id = $parent_id;
				}

				$builder_widgets = ether::meta('builder_data', TRUE, $id);

				if (defined('ICL_LANGUAGE_CODE') AND ( ! is_array($builder_widgets) OR empty($builder_widgets)))
				{
					global $sitepress;

					//this condition fixes compatibility with PolyLang plugin
					//otherwise the following error appears when trying to create a new page / edit page without any ether widgets initialized
					//Fatal error: Call to a member function get_default_language() on a non-object
					if ( isset($sitepress) )
					{
						if (ICL_LANGUAGE_CODE != $sitepress->get_default_language())
						{
							if ($post->post_status == 'auto-draft' AND isset($_GET['trid']) AND isset($_GET['source_lang']))
							{
								global $wpdb;
								global $table_prefix;

								$prefix = $table_prefix;

								$id = $wpdb->get_var('SELECT element_id FROM `'.$prefix.'icl_translations` WHERE `trid`=\''.$_GET['trid'].'\' AND `language_code`=\''.$_GET['source_lang'].'\'');
							} else
							{
								$id = icl_object_id($post->ID, $post->post_type, true, (isset($_GET['source_lang']) ? $_GET['source_lang'] : $sitepress->get_default_language()));
							}

							$builder_widgets = ether::meta('builder_data', TRUE, $id);
						}
					}
				}

				if (defined('ICL_LANGUAGE_CODE') AND is_string($builder_widgets))
				{
					// wpml breaks sometimes serialized data
					$builder_widgets = ether_builder::unserialize($builder_widgets);
				}

				foreach ($locations as $location => $name)
				{
					$builder_widgets_output = '';

					$body .= '<button name="builder-widget-add" class="builder-location-widget-add builder-widget-insert-position-before" style="display: none;"><span>'.ether::langr('Add widget').'</span></button>';
					$body .= '<div id="builder-location-'.$location.'" class="builder-location" style="display: none">';
					$body .= ether_builder::parse($builder_widgets, $location, TRUE, $builder_data);
					$body .= '</div>';
					$body .= '<button name="builder-widget-add" class="builder-location-widget-add builder-widget-insert-position-after" style="display: none;"><span>'.ether::langr('Add widget').'</span></button>';
				}

				$builder_tab_default = FALSE;

				if ( ! isset($_GET['post']) AND ether::option('builder_tab_default'))
				{
					$builder_tab_default = TRUE;
				}

				$body .= ether::make_field('editor_tab', array('type' => 'hidden', 'relation' => 'meta', 'use_default' => $builder_tab_default, 'value' => ($builder_tab_default ? 'builder' : '')));

				if ($tmp_post != NULL)
				{
					$post = $tmp_post;
				}

				$body .= '</fieldset></div>';
			}

			return $body;
		}
	}
}

?>
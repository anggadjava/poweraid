<?php

require('helper.php');

if ( ! class_exists('ether_builder'))
{
	class ether_builder extends ether_module
	{
		protected static $widget_classes = array();
		protected static $widget_data = array();
		protected static $widget_slugs = array();
		protected static $widgets = array();

		protected static $disabled_fields = array();
		protected static $locations = array();
		protected static $buffer = NULL;

		protected static $generated_content_registry = array();
		protected static $content_output = array();
		protected static $setup_done = FALSE;

		public static function init()
		{
			self::register_location('main', ether::langr('Main content'));
		}

		public static function widgets_init()
		{
			if (is_admin())
			{
				wp_enqueue_script('tiny_mce');
				wp_enqueue_style('editor-buttons');
			}

			self::$widget_classes = apply_filters('ether_builder_widgets', self::$widget_classes);

			$count = count(self::$widget_classes);

			for ($i = 0; $i < $count; $i++)
			{
				$class = self::$widget_classes[$i];

				if (class_exists($class))
				{
					$object = new $class();

					if ( ! isset(self::$widget_slugs[$object->get_slug()]))
					{
						self::$widgets[] = $object;
						self::$widget_slugs[$object->get_slug()] = $class;
					} else
					{
						unset($obejct);
					}
				}
			}
		}

		public static function builder_sidebar_widgets_init()
		{
			// deprecated by option builder_wp2builder_enable
			/*if (ether::option('builder_wp2builder_disable'))
			{
				return;
			}*/

			if (ether::option('builder_wp2builder_enable') != 'on')
			{
				return;
			}

			global $wp_widget_factory;

			$widgets = array_keys($wp_widget_factory->widgets);
			$wp_widgets = array();

			foreach ($wp_widget_factory->widgets as $widget_class => $widget_object)
			{
				if (substr($widget_class, 0, strlen(ether::config('prefix'))) == ether::config('prefix'))
				{
					continue;
				}

				$slug = ether::slug('wp_widget_'.$widget_object->name);

				// this widget from ultimatum theme is responsible for javascript errors
				// just skip it
				if ($slug == 'wp-widget-wordpress-default-loop' OR substr($slug, 0, 18) == 'wp-widget-facebook')
				{
					continue;
				}

				$wp_widgets[] = $slug;
			}

			$wp_widgets = apply_filters('ether_builder_wp_widgets', $wp_widgets);

			foreach ($wp_widget_factory->widgets as $widget_class => $widget_object)
			{
				$widget_slug = ether::slug('wp_widget_'.$widget_object->name);

				if (in_array($widget_slug, $wp_widgets))
				{
					$code = '';
					$code .= 'class '.$widget_class.'_builder_widget extends ether_wp_widget';
					$code .= '{';
					$code .= '	public function __construct()';
					$code .= '	{';
					$code .= '		parent::__construct(\''.$widget_slug.'\', \'WP '.addslashes($widget_object->name).'\');';
					$code .= '		$this->label = \''.addslashes(isset($widget_object->widget_options['description']) ? $widget_object->widget_options['description'] : '').'\';';
					$code .= '		$this->wp_class = \''.$widget_class.'\';';
					$code .= '		$this->wp_widget = new $this->wp_class;';
					$code .= '	}';
					$code .= '}';

					// evil!
					if ( ! eval_syntax_error($code))
					{
						eval($code);
						ether_builder::register_widget($widget_class.'_builder_widget');
					}
				}
			}
		}

		public static function sidebar_builder_widgets_init()
		{
			if (ether::option('builder_builder2wp_disable'))
			{
				return;
			}

			global $wp_widget_factory;

			// list of widgets from content builder included in sidebar widgets
			$builder_widgets = array
			(
				'ether_plain_text_widget',
				'ether_rich_text_widget',
				'ether_html_widget',
				'ether_heading_widget',
				'ether_image_widget',
				'ether_divider_widget',
				'ether_message_widget',
				'ether_blockquote_widget',
				'ether_list_widget',
				'ether_button_widget',
				'ether_video_widget',
				'ether_table_widget',
				'ether_tabs_widget',
				'ether_accordion_widget',
				'ether_gallery_widget',
				'ether_services_widget',
				'ether_testimonials_widget',
				'ether_twitter_feed_widget',
				'ether_googlemap_widget',
				'ether_contact_widget',
				'ether_pricing_table_widget',
				'ether_pricing_box_widget',
				'ether_fb_button_widget',
				'ether_fb_comments_widget',
				'ether_fb_likebox_widget'
			);

			$builder_widgets = apply_filters('ether_builder_sidebar_widgets', $builder_widgets);

			foreach ($builder_widgets as $index => $widget_class)
			{
				if ( ! in_array($widget_class, self::$widget_classes))
				{
					unset($builder_widgets[$index]);
				}
			}

			foreach ($builder_widgets as $widget_class)
			{
				$class = $widget_class;

				$code = '';
				$code .= 'class '.$class.'_sidebar_widget extends ether_builder_sidebar_widget';
				$code .= '{';
				$code .= '	public function __construct()';
				$code .= '	{';
				$code .= '		$this->builder_class = \''.$class.'\';';
				$code .= '		$this->builder_widget = new $this->builder_class;';
				$code .= '		parent::__construct(FALSE, \'&nbsp;'.ether::langr('Builder').' \'.$this->builder_widget->get_title(), array(\'description\' => $this->builder_widget->get_label()));';
				$code .= '	}';
				$code .= '}';

				// evil, ekhm i mean...

				eval($code);

				register_widget($class.'_sidebar_widget');

				// this action is ran after widgets_init and auto registration of widgets
				// so we have to register widgets manually
				$wp_widget_factory->widgets[$class.'_sidebar_widget']->_register();
			}
		}

		public static function header()
		{
			if (is_admin())
			{
				global $post_type;

				// if not add new post or edit post screen
				if (empty($post_type))
				{
					if (function_exists('add_thickbox'))
					{
						add_thickbox();
					}
				}
			} else
			{
				if (did_action('ether_builder_header') == 1)
				{
					$style = apply_filters('ether_builder_style', stripslashes(ether::option('builder_style')));

					if ( ! empty($style))
					{
						echo '<style type="text/css">'.$style.'</style>';
					}
				}
			}
		}

		public static function canvas()
		{
			global $post;

			if ( ! is_admin())
			{
				if (isset($post->ID) AND ether::meta('canvas', TRUE, $post->ID) == 'on' AND did_action('ether_canvas_init') == 0)
				{
					do_action('ether_canvas_init');
					?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<title><?php the_title(); ?></title>
<?php wp_head(); ?>
		<style>body { background-color: #e6e6e6; } .ether-canvas { width: 960px; margin: 20px auto; padding: 20px 40px; box-shadow: 0 2px 6px rgba(100, 100, 100, 0.3);  background-color: #fff; }</style>
	</head>

	<body <?php body_class(); ?>>
		<div class="ether-canvas">
			<?php the_content(); ?>
		</div>
	</body>
</html><?php
					wp_footer();

					do_action('shutdown');

					die;
				}
			}
		}

		public static function register_widget($class)
		{
			self::$widget_classes[] = $class;
		}

		public static function register_wp_widget($class, $title, $label)
		{
			self::$wp_widget_classes[] = $class;
		}

		public static function register_widget_data($slug, $title)
		{
			$slug = ether::slug($slug);

			self::$widget_data[] = array('slug' => $slug, 'title' => $title);
		}

		public static function get_disabled_fields()
		{
			return apply_filters('ether_builder_disabled_fields', self::$disabled_fields);
		}

		public static function disable_field($name)
		{
			if ( ! in_array($name, self::$disabled_fields))
			{
				self::$disabled_fields[] = $name;
			}
		}

		public static function get_widgets($sort = FALSE)
		{
			if ( ! $sort)
			{
				return self::$widgets;
			} else
			{
				$gridslider_widgets = array('post-feed', 'page-feed', 'custom-feed', 'gallery', 'services', 'testimonials', 'pricing-table', 'twitter-feed', 'flickr', 'pricing-box');
				$third_party_slider_widgets = array('nivo', 'roundabout');

				$widgets_data = array(
					'col' => array(
						'name' => ether::langr('Columns'),
						'widgets' => array(),
						'tooltip' => ether::langr('Container Widgets. Other widgets can be inserted inside them to achieve more complex layouts')
					),
					'gridslider' => array(
						'name' => ether::langr('Grid-Sliders'),
						'widgets' => array(),
						'tooltip' => ether::langr('Widgets featuring Grid & Slider capabilities. Content inside them can be easily organised into either static or slideable grids with plenty of custom options')
					),
					'classic' => array(
						'name' => ether::langr('Classic'),
						'widgets' => array()
					),
					'third_party_slider' => array(
						'name' => ether::langr('3rd Party Sliders'),
						'widgets' => array()
					),
					'wp' => array(
						'name' => ether::langr('WordPress & 3rd Party'),
						'widgets' => array(),
						'tooltip' => ether::langr('Default Wordpress Widgets, Theme & Plugin widgets that are available in sidebars. NOTE: Some widgets that do not follow WordPress Codex can sometimes break Ether Content Builder. In such cases we advise contacting the author of the faulty plugin/theme in question first')
					)
				);
				
				$widgets = self::$widgets;

				foreach ($widgets as $widget)
				{
					$slug = $widget->get_slug();
					$target = null;

					if (strpos($slug, 'row') !== FALSE)
					{
						$target = 'col';
					} else if (in_array($slug, $gridslider_widgets))
					{
						$target = 'gridslider';
					} else if (in_array($slug, $third_party_slider_widgets))
					{
						$target = 'third_party_slider';
					} else if (strpos($slug, 'wp-widget-') !== FALSE)
					{
						$target = 'wp';
					} else
					{
						$target = 'classic';
					}

					isset($target) ? ($widgets_data[$target]['widgets'][] = $widget) : '';
				}

				return $widgets_data;
			}
		}

		public static function get_widget_class($slug)
		{
			$slug = ether::slug($slug);

			if (isset(self::$widget_slugs[$slug]))
			{
				return self::$widget_slugs[$slug];
			}

			return '';
		}

		public static function get_widget_title($slug)
		{
			$slug = ether::slug($slug);

			foreach (self::$widget_data as $widget_data)
			{
				if ($widget_data['slug'] == $slug)
				{
					return $widget_data['title'];
				}
			}

			return ether::langr('Widget');
		}

		public static function register_location($slug, $name)
		{
			$slug = ether::slug($slug);

			if ( ! isset(self::$locations[$slug]))
			{
				self::$locations[$slug] = $name;

				return TRUE;
			}

			return FALSE;
		}

		public static function get_locations()
		{
			return self::$locations;
		}

		public static function extract_widgets($arr, $skip_fields = array())
		{
			$output = array();

			foreach ($arr as $key => $value)
			{
				if (isset($value['__SLUG__']))
				{
					if ( ! isset($value['__CORE__']))
					{
						if ( ! empty($skip_fields))
						{
							foreach ($skip_fields as $field)
							{
								if (isset($output[$field]))
								{
									unset($output[$field]);
								}
							}
						}

						$output[$key] = $value;
					}
				} else
				{
					$output = array_merge($output, self::extract_widgets($value, $skip_fields));
				}
			}

			return $output;
		}

		public static function flatten($builder, $prefix = '', $parse = TRUE)
		{
			$output = array();

			if (is_array($builder))
			{
				foreach ($builder as $key => $value)
				{
					$entry = ($prefix ? $prefix.'][' : '').$key;

					if (is_array($value))
					{
						$output = array_merge($output, self::flatten($value, $entry, FALSE));
					} else
					{
						$output['['.$entry.']'] = $value;
					}
				}

				if ($parse)
				{
					$builder = $output;
					$output = array();

					foreach ($builder as $k => $v)
					{
						preg_match_all('/\[(.*?)\]\[(.*?)\]\[(.*?)\]\[(.*?)\]\[(.*?)\]/is', $k, $matches);

						if (isset($matches[4][0]) AND ! empty($matches[4][0]) AND isset($matches[5][0]) AND ! empty($matches[5][0]))
						{
							$id = $matches[4][0];
							$field = $matches[5][0];

							if ( ! isset($output[$id]))
							{
								$output[$id] = array();
							}

							$output[$id][$field] = $v;
						}
					}
				}
			}

			return $output;
		}

		public static function parse($builder_widgets, $location, $admin = FALSE, $data = array())
		{
			$builder_widgets_output = '';

			$row_begin = FALSE;
			$first_row_data = array();
			$row_id = '__ID__';
			$after_buffer = array();

			if (isset($builder_widgets[$location]) AND is_array($builder_widgets[$location]))
			{
				foreach ($builder_widgets[$location] as $row_name => $row_data)
				{
					if ($row_name !== '__ROW__')
					{
						$columns = array(array(), array(), array(), array());

						foreach ($row_data as $column_name => $column_data)
						{
							if ($column_name === '__COLUMN__')
							{
								foreach ($column_data as $widget_id => $widget_data)
								{
									$is_hidden = (isset($widget_data['is_hidden']) && $widget_data['is_hidden'] == 'on');

									if ( ! isset($widget_data['__CORE__']))
									{
										if ($widget_id != '__ID__')
										{
											$class = self::get_widget_class($widget_data['__SLUG__']);

											if ( ! empty($class) AND class_exists($class))
											{
												$object = new $class();
												$object->set_id($widget_id);
												$object->set_location($location);
												$object->set_row($row_name);
												$object->set_column($column_name);

												if ($admin)
												{
													$builder_widgets_output .= $object->admin_form(array_merge($widget_data, isset($data[$widget_id]) ? $data[$widget_id] : array(), array('__ID__' => $widget_id)));
												} else
												{

													$widget_data = array_merge($widget_data, isset($data[$widget_id]) ? $data[$widget_id] : array(), array('__ID__' => $widget_id));

													if ($is_hidden)
													{
														continue;
													}

													$output = '';

													if ($object->is_after())
													{
														$after_buffer[] = array('widget' => $object, 'widget_data' => $widget_data, 'widget_id' => $widget_id);

														$builder_widgets_output .= '<!--ETHER_BUILDER_WIDGET_AFTER_'.$widget_id.'-->';
													} else
													{
														$builder_widgets_output = $object->content_filter($widget_data, $builder_widgets_output);

														$output = $object->widget($widget_data);
														$output = apply_filters('ether_builder_widget', $output, $widget_id, $widget_data, $object->get_slug());
														$output = apply_filters('ether_builder_'.$object->get_slug().'_widget', $output, $widget_id, $widget_data, $object->get_slug());

														$builder_widgets_output .= $output;
													}
												}

												$row_begin = FALSE;
											}
										}
									} else
									{
										if ( ! $admin && $is_hidden)
										{
											continue;
										}

										$row_id = $widget_id;
										$row_begin = TRUE;
										$first_row_data = array_merge($widget_data, array('__ID__' => $widget_id));
									}
								}
							} else
							{
								foreach ($column_data as $widget_id => $widget_data)
								{
									$is_hidden = (isset($widget_data['is_hidden']) && $widget_data['is_hidden'] == 'on');

									if ( ! $admin && $is_hidden)
									{
										continue;
									}

									$columns[$column_name][] = array_merge($widget_data, array('__ID__' => $widget_id));
								}
							}
						}

						if ($row_begin)
						{
							$row_columns = array();

							foreach ($columns as $column_index => $column_data)
							{
								foreach ($column_data as $widget)
								{
									$is_hidden = (isset($widget['is_hidden']) && $widget['is_hidden'] == 'on');

									if ( ! $admin && $is_hidden)
									{
										continue;
									}

									$class = self::get_widget_class($widget['__SLUG__']);

									if ( ! empty($class) AND class_exists($class))
									{
										$object = new $class();
										$object->set_id($widget['__ID__']);
										$object->set_location($location);
										$object->set_row($row_name);
										$object->set_column($column_index);

										if ( ! isset($row_columns['col-'.($column_index + 1)]))
										{
											$row_columns['col-'.($column_index + 1)] = '';
										}

										if ($admin)
										{
											$row_columns['col-'.($column_index + 1)] .= $object->admin_form(array_merge($widget, isset($data[$widget['__ID__']]) ? $data[$widget['__ID__']] : array()));
										} else
										{
											$widget_id = $widget['__ID__'];
											$widget_data = array_merge($widget, isset($data[$widget['__ID__']]) ? $data[$widget['__ID__']] : array());
											$output = '';

											if ($object->is_after())
											{
												$after_buffer[] = array('widget' => $object, 'widget_data' => $widget_data, 'widget_id' => $widget_id);

												$output = '<!--ETHER_BUILDER_WIDGET_AFTER_'.$widget_id.'-->';

												$row_columns['col-'.($column_index + 1)] .= $output;
											} else
											{
												$builder_widgets_output = $object->content_filter($widget_data, $builder_widgets_output);

												$output = $object->widget($widget_data);

												$output = apply_filters('ether_builder_widget', $output, $widget_id, $widget_data, $object->get_slug());
												$output = apply_filters('ether_builder_'.$object->get_slug().'_widget', $output, $widget_id, $widget_data, $object->get_slug());

												$row_columns['col-'.($column_index + 1)] .= $output;
											}
										}
									}
								}
							}

							$class = self::get_widget_class($first_row_data['__SLUG__']);

							if ( ! empty($class) AND class_exists($class))
							{
								$object = new $class();

								$object->set_id($row_id);
								$object->set_location($location);
								$object->set_row($row_name);

								if ($admin)
								{
									$builder_widgets_output .= $object->admin_form(array_merge(array('__ID__' => $row_id), $row_columns, $first_row_data));
								} else
								{
									$output = $object->widget(array_merge(array('__ID__' => $row_id), $row_columns, $first_row_data));
									$output = apply_filters('ether_builder_widget', $output, $row_id, array(), $object->get_slug());
									$output = apply_filters('ether_builder_'.$object->get_slug().'_widget', $output, $row_id, array(), $object->get_slug());

									$builder_widgets_output .= $output;
								}
							}
						}
					}
				}
			}

			if ( ! $admin)
			{
				foreach ($after_buffer as $after)
				{
					$builder_widgets_output = $after['widget']->content_filter($after['widget_data'], $builder_widgets_output);

					$output = $after['widget']->widget($after['widget_data']);

					$output = apply_filters('ether_builder_widget', $output, $after['widget_id'], $after['widget_data'], $after['widget']->get_slug());
					$output = apply_filters('ether_builder_'.$after['widget']->get_slug().'_widget', $output, $after['widget_id'], $after['widget_data'], $after['widget']->get_slug());

					$builder_widgets_output = str_replace('<!--ETHER_BUILDER_WIDGET_AFTER_'.$after['widget_id'].'-->', $output, $builder_widgets_output);
				}

				if (function_exists('qtrans_init'))
				{
					return ether::langr($builder_widgets_output);
				} else
				{
					return $builder_widgets_output;
				}
			}

			return $builder_widgets_output;
		}

		public static function bootstrap($template)
		{
			global $post;
			$custom_template = ether::meta('template', TRUE, $post->ID);

			if ( ! empty($custom_template))
			{
				$dynamic_template = ether::bootstrap_template_exists('dynamic.php');

				if ($dynamic_template !== FALSE)
				{
					$template = $dynamic_template;
					self::$buffer = ether::meta('builder_data', TRUE, $custom_template);
				}
			}

			return $template;
		}

		public static function get($location)
		{
			global $post;

			if (self::$buffer != NULL)
			{
				$buffer = ether::meta('builder_data', TRUE, $post->ID);
				$data = array();

				// overwrite builder data with custom data
				//$data = ether::meta('builder_widget_post_data', TRUE, $post->ID);

				echo self::parse($buffer, $location, FALSE);
			}
		}

		// template tag, use it in custom query loops
		public static function get_the_content($id = NULL, $skip_the_content = FALSE)
		{
			global $post;

			if (did_action('ether_builder_header') == 0)
			{
				do_action('ether_builder_header');
			}

			$id = $id !== NULL ? $id : $post->ID;

			if (post_password_required($id))
			{
				return '';
			}

			$data = ether::meta('builder_data', TRUE, $id);

			$data_flatten = self::flatten($data, '', FALSE);
			$prepend_content = TRUE;

			foreach ($data_flatten as $k => $v)
			{
				// if content widget found in buffer, print empty content for now
				if ($v == 'post-content' AND strpos($k, '__SLUG__') !== FALSE)
				{
					$prepend_content = FALSE;
					break;
				}
			}

			return (($prepend_content AND ! $skip_the_content) ? apply_filters('the_content', get_the_content()) : '').self::parse($data, 'main', FALSE);
		}

		public static function the_content($id = NULL, $skip_the_content = FALSE)
		{
			echo self::get_the_content($id);
		}

		public static function builder_prototypes()
		{
			global $post;
			global $post_type;

			$builder_metabox = ether::admin_metabox_get('Builder post');

			if ( ! empty($builder_metabox) AND in_array($post_type, $builder_metabox['permissions']))
			{
				echo ether_metabox_builder::get_prototypes();
			}
		}

		public static function builder_previews()
		{
			global $post;
			global $post_type;

			$builder_metabox = ether::admin_metabox_get('Builder post');
			$screen = get_current_screen();

			if (( ! empty($builder_metabox) AND in_array($post_type, $builder_metabox['permissions'])) OR $screen->id == 'widgets')
			{
				echo ether_metabox_builder::get_previews();
			}
		}

		// HACK #1
		public static function builder_tab($content)
		{
			global $post;
			global $post_type;

			$builder_metabox = ether::admin_metabox_get('Builder post');

			if ( ! empty($builder_metabox) AND in_array($post_type, $builder_metabox['permissions']))
			{
				if (strpos($content, 'editorcontainer') !== FALSE OR strpos($content, 'wp-content-editor-container') !== FALSE)
				{
					echo '<div id="editor-builder-tab" class="hide-if-no-js hide">'.ether_metabox_builder::body().'</div>';
				}
			}

			return $content;
		}

		// HACK #2
		// CHECK HACK#3
		/*public static function editor_content($content)
		{
			global $post;
			global $post_type;

			if ($post AND $post->post_type != 'section')
			{
				if ( ! empty($post->ID) AND ! isset(self::$generated_content_registry[$post->ID]))
				{
					// "last" tab hack
					self::$generated_content_registry[$post->ID] = TRUE;
					// content builder structure data
					$buffer = ether::meta('builder_data', TRUE, $post->ID);

					if ( ! empty($buffer))
					{
						$buffer_flatten = self::flatten($buffer, '', FALSE);

						foreach ($buffer_flatten as $k => $v)
						{
							// if content widget found in buffer, print empty content for now
							if ($v == 'post-content' AND strpos($k, '__SLUG__') !== FALSE)
							{
								$content = '';

								break;
							}
						}

						$builder_content = self::parse($buffer, 'main', FALSE);

						$content .= $builder_content;
					}
				}
			}

			return $content;
		}*/

		public static function builder_content($content)
		{
			global $post;
			global $post_type;

			if ( ! self::$setup_done)
			{
				self::builder_setup();
			}

			if ($post AND ! post_password_required($post))
			{
				if ( ! empty($post->ID) AND ! isset(self::$generated_content_registry[$post->ID]))
				{
					if (isset(self::$content_output[$post->ID]))
					{
						$content .= _n.do_shortcode(self::$content_output[$post->ID]);
						//unset(self::$content_output[$post->ID]);
					}
				} else
				{
					// builder content found and contains post content widget, overwrite first the_content call
					if (isset(self::$content_output[$post->ID]))
					{
						$content = _n.do_shortcode(self::$content_output[$post->ID]);
						//unset(self::$content_output[$post->ID]);
					}
				}
			}

			return $content;
		}

		// HACK #3
		// setup content and parse all widgets before the_content action
		// some of the widgets may include additional scripts or stylesheets
		// or change output of the_content/the_title
		public static function builder_setup()
		{
			global $post;
			global $post_type;
			global $wp_query;

			if ( ! is_singular() AND ether::config('builder_archive_hide'))
			{
				return;
			}

			$tmp_post = $post;

			$posts = array();

			if (isset($wp_query->posts))
			{
				$posts = $wp_query->posts;
			} else if ( ! empty($post))
			{
				$posts[] = $post;
			}

			if (empty($posts))
			{
				return;
			}

			// get rid of setup_done, sometimes builder_setup() must be called twice for multiple queries/posts
			//self::$setup_done = TRUE;

			foreach ($posts as $post)
			{
				if ($post)
				{
					if ( ! empty($post->ID) AND ! isset(self::$content_output[$post->ID]))
					{
						// content builder structure data

						$id = $post->ID;
						$data = array();

						/*if ($post->post_type == 'project' AND $post->post_parent > 0)
						{
							$id = $post->post_parent;

							$data = ether::meta('builder_widget_post_data', TRUE, $post->ID);
						}*/

						$buffer = ether::meta('builder_data', TRUE, $id);

						self::$content_output[$post->ID] = '';

						if ( ! empty($buffer))
						{
							$builder_content = self::parse($buffer, 'main', FALSE, $data);

							$buffer_flatten = self::flatten($buffer, '', FALSE);

							foreach ($buffer_flatten as $k => $v)
							{
								// if content widget found in buffer, print empty content for now
								if ($v == 'post-content' AND strpos($k, '__SLUG__') !== FALSE)
								{
									self::$generated_content_registry[$post->ID] = TRUE;

									break;
								}
							}

							self::$content_output[$post->ID] = $builder_content;

							if ( ! empty(self::$content_output[$post->ID]))
							{
								if ( ! is_admin() AND did_action('ether_builder_header') == 0)
								{
									do_action('ether_builder_header');
								}
							}
						}
					}
				}
			}

			$post = $tmp_post;

			self::canvas();
		}

		public static function builder_search($where)
		{
			global $wp_query;
			global $wpdb;
			global $wp;

			if ( ! is_admin() AND $wp_query->is_search)
			{
				$query = str_replace('/', '\/', $wp->query_vars['s']);

				$where = preg_replace("/(posts.post_title (LIKE '%{$query}%'))/i", "$0) OR ($wpdb->postmeta.meta_key = 'ether_builder_data' AND $wpdb->postmeta.meta_value LIKE '%{$query}%'", $where);
			}

			return $where;
		}

		public static function builder_search_join($join)
		{
			global $wp_query;
			global $wpdb;
			global $wp;

			if ( ! is_admin() AND $wp_query->is_search)
			{
				if ( ! preg_match("/(on)( |\()*($wpdb->postmeta.post_id)( )*(=)( )*($wpdb->posts.ID)/i", $join) AND ! preg_match("/(on)( |\()*($wpdb->posts.ID)( )*(=)( )*($wpdb->postmeta.post_id)/i", $join))
				{
					return $join .= " LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id) ";
				}
			}

			return $join;
		}

		public static function builder_search_distinct($distinct)
		{
			$distinct = 'DISTINCT';

			return $distinct;
		}

		public static function unserialize($data)
		{
			// replace timestamps stored as integers to strings
			return ether::unserialize(preg_replace('!i:([0-9]{13,20}?);!e', "'s:'.strlen('$1').':\"$1\";'", $data));
		}

		public static function unserialize_fix($null, $object_id, $meta_key, $single)
		{
			$meta_type = 'post';

			if ($meta_key == 'ether_builder_data')
			{
				$meta_cache = wp_cache_get($object_id, $meta_type.'_meta');

				if ( ! $meta_cache)
				{
					$meta_cache = update_meta_cache($meta_type, array($object_id));
					$meta_cache = $meta_cache[$object_id];
				}

				if (isset($meta_cache[$meta_key]))
				{
					if ($single)
					{
						if (is_serialized($meta_cache[$meta_key][0]) AND ! maybe_unserialize($meta_cache[$meta_key][0]))
						{
							return array(self::unserialize($meta_cache[$meta_key][0]));
						}
					} else
					{
						foreach ($meta_cache[$meta_key] as $k => $v)
						{
							if (is_serialized($v) AND ! maybe_unserialize($v))
							{
								return array_map(array('ether_builder', 'unserialize'), $meta_cache[$meta_key]);
							}
						}
					}
				}
			}
		}

		public static function tinymce_quiet_setup()
		{
			// this piece of sh... code provides tinymce initialization when visual editing is disabled
			// or tinymce is not initialized in the current admin screen
			// it's required for builder widgets using rich editing. always!

			//wp_print_styles('editor-buttons'); // why im not able to enqueue style editor-buttons?

			$screen = get_current_screen();

			if ( ! empty($screen->post_type) OR $screen->id == 'widgets')
			{
				add_filter('user_can_richedit', '__return_true');
				ob_start();
				wp_editor('dummy_editor_only_for_tinymce_initialization', 'etherbuildereditorfix');
				$dirty_solution = ob_get_clean();
				remove_filter('user_can_richedit', '__return_true');
			}
		}
	}
}

if ( ! class_exists('ether_builder_widget'))
{
	class ether_builder_widget
	{
		protected $id;
		protected $slug;
		protected $title;
		protected $location;
		protected $row;
		protected $column;
		protected $core;
		protected $label;
		protected $excerpt;
		protected $visible;

		// parse this widget at the end
		protected $after;

		// some private data, special options or smth
		protected $data;

		protected static $defaults = array
		(
			'img_title' => array
			(
				'enable_title' => '',//deprecated
				'img_title_pos_y' => '',//deprecated
				'always_show_img_title' => '',//deprecated
				'show_img_title' => '',
				'img_title_alignment_y' => 'bottom',
				'img_title_alignment_x' => ''
			),
			'img_align_fit' => array
			(
				'image_mode' => 'auto',
				'image_alignment_x_parent' => '',
				'image_alignment_y_parent' => '',
			),
			'img_size' => array
			(
				'image_width' => '',
				'image_height' => '',
				'image_crop_width' => '',
				'image_crop_height' => '',
			),
			'grid_settings' => array
			(
				'cols' => '',
				'rows' => '',
				'disable_spacing' => '',
				'col_spacing_size' => ''
			),
			'widget_pos_align' => array
			(
				'width' => '',
				'align' => '',
			),
			'widget_clearfloat' => array
			(
				'clearfloat' => ''
			),
			'widget_visibility' => array
			(
				'is_hidden' => ''
			)
		);

		public function __construct($slug, $title, $id = NULL, $location = NULL, $row = NULL, $column = NULL)
		{
			$this->core = FALSE;
			$this->slug = ether::slug($slug);
			$this->title = $title;

			$this->id = $id;
			$this->location = $location;
			$this->row = $row;
			$this->column = $column;
			$this->label = '';
			$this->excerpt = '';
			$this->visible = TRUE;
			$this->after = FALSE;
			$this->after_id = '';

			if ($this->id == NULL)
			{
				$this->id = '__ID__';
			}

			if ($this->location == NULL)
			{
				$this->location = '__LOCATION__';
			}

			if ($this->row == NULL)
			{
				$this->row = '__ROW__';
			}

			if ($this->column == NULL)
			{
				$this->column = '__COLUMN__';
			}

			ether_builder::register_widget_data($slug, $title);
		}

		public function is_core()
		{
			return $this->core;
		}

		public function is_generic_container()
		{
			return isset($this->generic_container);
		}

		public function get_field_name($name)
		{
			return ether::config('prefix').'builder_widget['.$this->location.']['.$this->row.']['.$this->column.']['.$this->id.']['.$name.']';
		}

		public function get_field_atts($name)
		{
			$disabled_fields = ether_builder::get_disabled_fields();

			if (in_array($name, $disabled_fields))
			{
				return ' disabled="disabled"';
			}

			return '';
		}

		public function strip($data, $valid_tags = NULL)
		{
			$regexp = '#\s*<(/?\w+)\s+(?:on\w+\s*=\s*(["\'\s])?.+?\(\1?.+?\1?\);?\1?|style=["\'].+?["\'])\s*>#is';

			return preg_replace($regexp, '<${1}>', strip_tags($data, $valid_tags));
		}

		public function encode($code)
		{
			return htmlentities($code, ENT_NOQUOTES);
		}

		public function decode($code)
		{
			return html_entity_decode($code, ENT_NOQUOTES);
		}

		public function attr($attr = array())
		{
			if (is_array($attr))
			{
				$attrs = '';

				foreach($attr as $key => $value)
				{
					if ($value === TRUE)
					{
						$attrs .= ' '.$key;
					} elseif (is_array($value) OR trim($value) !== '')
					{
						if (is_array($value))
						{
							$attrs .= ' '.$key.'="'.$this->strip(implode(' ', $value)).'"';
						} else
						{
							$attrs .= ' '.$key.'="'.$this->strip($value).'"';
						}
					}
				}
			} else
			{
				$attrs = ' '.$attr;
			}

			$attrs = trim($attrs);

			return ( ! empty($attrs) ? ' ' : '').$attrs;
		}

		public function _class($classes = array(), $no_prefix_classes = array())
		{
			if ( ! is_array($classes))
			{
				$classes = explode(' ', $classes);
			}

			if ( ! is_array($no_prefix_classes))
			{
				$no_prefix_classes = explode(' ', $no_prefix_classes);
			}

			$class = '';

			if ( ! empty($classes))
			{
				$_classes = array();

				foreach ($classes as $c)
				{
					$c = trim($c);

					if ( ! empty($c))
					{
						$_classes[] = $c;
					}
				}

				$class = ether::config('builder_widget_prefix').implode(' '.ether::config('builder_widget_prefix'), $_classes);
			}

			if ( ! empty($no_prefix_classes))
			{
				$_no_prefix_classes = array();

				foreach ($no_prefix_classes as $c)
				{
					$c = trim($c);

					if ( ! empty($c))
					{
						$_no_prefix_classes[] = $c;
					}
				}

				$class .= ( ! empty($class) ? ' ' : '').implode(' ', $_no_prefix_classes);
			}

			return ( ! empty($class) ? ' class="'.trim($class).'"' : '');
		}

		public function gen_html($widget, $key)
		{
			$output = '';

			switch ($key)
			{
				case 'cols':
				{
					$classes = array('cols');
					$classes[] = 'cols-'.$widget['columns'];
					$classes[] = 'rows-'.$widget['rows'];
					$classes[] = 'spacing-'.(isset($widget['disable_spacing']) && $widget['disable_spacing'] == 'on' ? 0 : 1);
					isset($widget['col_spacing_size']) && ! empty($widget['col_spacing_size']) ? $classes[] = 'spacing-size-'.$widget['col_spacing_size'] : '';

					return '<div'.$this->_class($classes).'>';
				}
			}
		}


		public function tag_open($tag, $attr = array(), $single = FALSE)
		{
			return '<'.$tag.$this->attr($attr).($single ? ' /' : '').'>';
		}

		public static function tag_close($tag, $single = FALSE)
		{
			if ($single)
			{
				return '';
			}

			return '</'.$tag.'>';
		}

		public static function tag($tag, $content = '', $attr = array(), $single = FALSE)
		{
			return $this->tag_open($tag, $attr, $single).$content.$this->tag_close($tag, $single);
		}

		public function field($type, $name, $data = NULL, $options = array())
		{
			$value = '';

			$default_class = str_replace(array('_', '[', ']'), array('-'), 'builder-'.$this->get_slug().'-widget-'.$name.'-field');
			$options['class'] = (isset($options['class']) ? $options['class'].' '.$default_class : $default_class);

			if (is_array($data))
			{
				$key = $name;
				$key = str_replace(array('[', ']'), array('', ''), $key);

				if (isset($data[$name]))
				{
					$value = $data[$name];
				}
			} else
			{
				$value = $data;
			}

			if ($type == 'select')
			{
				$option_list = '';

				if (isset($options['options']))
				{
					foreach ($options['options'] as $k => $v)
					{
						$option_list .= '<option value="'.$k.'"'.($value == $k ? ' selected="selected"' : '').'>'.$v.'</option>';
					}
				} else
				{
					if ( ! empty($value))
					{
						$option_list .= '<option value="'.$value.'" selected="selected">'.$value.'</option>';
					} else
					{
						$option_list .= '<option></option>';
					}
				}

 				return '<select'.$this->get_field_atts($name).' name="'.$this->get_field_name($name).'" value="'.$value.'"'.((isset($options['class']) AND ! empty($options['class'])) ? ' class="'.$options['class'].'"' : '').(isset($options['disabled']) ? ' disabled="'.$options['disabled'].'"' : '').'>'.$option_list.'</select>';
			} else if ($type == 'textarea')
			{
				if ( ! isset($options['rows']) OR empty($options['rows']))
				{
					$options['rows'] = 5;
				}

				return '<textarea'.$this->get_field_atts($name).' name="'.$this->get_field_name($name).'"'.((isset($options['rows']) AND ! empty($options['rows'])) ? ' rows="'.$options['rows'].'"' : '').((isset($options['cols']) AND ! empty($options['cols'])) ? ' cols="'.$options['cols'].'"' : '').((isset($options['class']) AND ! empty($options['class'])) ? ' class="'.$options['class'].'"' : '').(isset($options['disabled']) ? ' disabled="'.$options['disabled'].'"' : '').'>'.htmlspecialchars($value).'</textarea>';
			} else
			{
				return '<input'.$this->get_field_atts($name).' name="'.$this->get_field_name($name).'" type="'.$type.'"'.((isset($options['class']) AND ! empty($options['class'])) ? ' class="'.$options['class'].'"' : 'cock').($type == 'checkbox' ? ($value == 'on' ? ' checked="checked"' : '') : ' value="'.$value.'"').(isset($options['disabled']) ? ' disabled="'.$options['disabled'].'"' : '').' />';
			}
		}

		public function group_field($type, $name, $index, $data = NULL, $options = array())
		{
			if ($data == NULL OR ! isset($data[$name]) OR ! isset($data[$name][$index]))
			{
				$value = NULL;
			} else
			{
				$value = $data[$name][$index];
			}

			return $this->field($type, $name.'][', $value, $options);
		}

		public function group_item($widget, $index)
		{
			return '';
		}

		public function is_after()
		{
			return $this->after;
		}

		public function get_slug()
		{
			return $this->slug;
		}

		public function get_title($widget = NULL)
		{
			return (isset($widget) AND isset($widget['admin-label']) AND ! empty($widget['admin-label'])) ? $widget['admin-label'] : $this->title;
		}

		public function get_id()
		{
			return $this->id;
		}

		public function set_id($id)
		{
			$this->id = $id;
		}

		public function get_location()
		{
			return $this->location;
		}

		public function set_location($location)
		{
			$this->location = $location;
		}

		public function get_row()
		{
			return $this->row;
		}

		public function set_row($row)
		{
			$this->row = $row;
		}

		public function get_column()
		{
			return $this->column;
		}

		public function set_column($column)
		{
			$this->column = $column;
		}

		public function get_label()
		{
			return $this->label;
		}

		public function get_excerpt()
		{
			return '';
		}

		public function get_summary($widget)
		{
			return '';
		}

		public function get_grid_slider_summary($widget)
		{
			return ether::langr('Cols: ').(isset($widget['columns']) ? $widget['columns'] : 1).' '.ether::langr('Rows: ').(isset($widget['rows']) ? $widget['rows'] : 1).' '.ether::langr('Slider: ').(isset($widget['slider']) && $widget['slider'] == 'on' ? ether::langr('Yes') : ether::langr('No'));
		}

		public function get_widget_location_preview($widget)
		{
			return '';
		}

		public function set_label($label)
		{
			$this->label = $label;
		}

		public function show()
		{
			$this->visible = TRUE;
		}

		public function hide()
		{
			$this->visible = FALSE;
		}

		// this method was initially added for "heading menu" widget
		// so the widget can iteract and modify currently generated content
		public function content_filter($widget, $content)
		{
			return $content;
		}

		public function widget($widget)
		{
			return '';
		}

		public function form($widget)
		{
			return '';
		}

		public function form_after($widget)
		{
			return '';
		}

		public function r_widget_alignment_class($widget)
		{
			$output = '';

			if (isset($widget['align']) && ! empty($widget['align']))
			{
				$output .= ' builder-align'.$widget['align'];
			}

			return $output;
		}

		public function r_widget_clearfloat_class($widget)
		{
			$output = '';

			if (isset($widget['clearfloat']) && ! empty($widget['clearfloat']))
			{
				$output .= ' builder-clearfloat-indicator';
			}

			return $output;
		}

		public function r_widget_visibility_class($widget)
		{
			$output = '';

			if ($this->is_hidden($widget))
			{
				$output .= ' builder-hidden-widget';
			}

			return $output;
		}

		public function r_widget_width_styles($widget)
		{
			$output = '';
			$width;

			if ( isset($widget['width']) && ! empty($widget['width']))
			{
				$width = ether::unit($widget['width'], 'px');
			}

			if (isset($width))
			{
				$output .= 'width: '.$width.';';
			}

			return $output;
		}

		public function admin_form($widget = NULL)
		{
			$classes = array(
				'builder-widget-wrapper',
				'column-1',
				'builder-widget-type-'.$this->get_slug()
			);
			$classes[] = ($this->id != '__ID__' ? 'initialized ' : '');
			$classes[] = ($this->is_core() ? 'builder-widget-core ' : '');
			$classes[] = ($this->is_core() && ! $this->is_generic_container() ? 'compact-front-display' : '');
			$classes[] = ($this->is_core() && $this->is_generic_container() ? 'builder-widget-type-row-'.$this->get_slug() : '');
			$classes[] = ( ! $this->visible ? ' hide' : '');
			$classes[] = $this->r_widget_alignment_class($widget);
			$classes[] = $this->r_widget_clearfloat_class($widget);
			$classes[] = $this->r_widget_visibility_class($widget);

			return '<div class="'.implode(' ', $classes).'" style="'.$this->r_widget_width_styles($widget).'">
				<div class="builder-widget">
					<div class="builder-widget-actions">
						<a href="#" class="show-menu dashicons dashicons-admin-generic">'.ether::langr('Show actions').'</a>
						<a href="#" class="remove dashicons dashicons-trash">'.ether::langr('Remove').'</a>
						<a href="#" class="move-up dashicons dashicons-arrow-up">'.ether::langr('Move Up').'</a>
						<a href="#" class="move-down dashicons dashicons-arrow-down">'.ether::langr('Move Down').'</a>
						<a href="#" class="toggle-visibility dashicons dashicons-visibility">'.ether::langr('Toggle Visibility').'</a>
						'.($this->is_core() ? '<a href="#" class="unwrap-row dashicons dashicons-external">'.ether::langr('Unwrap Column').'</a>' : '').'
						<a href="#" class="duplicate dashicons dashicons-download">'.ether::langr('Duplicate').'</a>
						<a href="#" class="edit dashicons dashicons-admin-appearance">'.ether::langr('Edit').'</a>
					</div>
					<div class="builder-widget-bar widget widget-top '.($this->is_core() ? 'builder-core-widget-bar' : '' ).'">
						<div class="builder-widget-bar-info">
							<div class="builder-widget-icon builder-widget-icon-'.$this->get_slug().( ! $this->visible ? ' hide' : '').'"></div>
							<div class="builder-widget-title">'.$this->get_title($widget).'</div>
							<div class="builder-widget-summary">'.$this->get_summary($widget).'</div>
							<div class="builder-widget-label ether-tooltip-content-raw">'.$this->get_label().'</div>
						</div>
						<div class="builder-widget-location-preview">'.$this->get_widget_location_preview($widget).'</div>
					</div>
					<div class="builder-widget-content closed">
						<div class="builder-widget-content-bar widget widget-top">
							<div class="builder-widget-title">'.$this->get_title($widget).'</div>
						</div>
						<div class="builder-widget-inner">
							<div class="builder-widget-content-form">'.$this->form($widget).'</div>
						</div>
						<div class="builder-widget-content-actions">
							<button name="builder-widget-save" class="save">'.ether::langr('Save').'</button>
							<button name="builder-metabox-cancel" class="cancel">'.ether::langr('Cancel').'</button>
						</div>
					</div>
					<input type="hidden" name="'.ether::config('prefix').'builder_widget['.$this->location.']['.$this->row.']['.$this->column.']['.$this->id.'][__SLUG__]" value="'.$this->get_slug().'" />'.($this->is_core() ? $this->form_after($widget).'<input type="hidden" name="'.ether::config('prefix').'builder_widget['.$this->location.']['.$this->row.']['.$this->column.']['.$this->id.'][__CORE__]" value="true" />' : '').'
				</div>
			</div>'._n;
		}

		public function admin_preview()
		{
			return '';
		}

		public function wp_admin_form($widget, $id_base, $number)
		{
			$form = $this->admin_form($widget);
			$atts = array('name', 'id');

			foreach ($atts as $attr)
			{
				preg_match_all('| '.$attr.'=\"'.ether::config('prefix').'builder_widget\[(.*)\]\[(.*)\]\[(.*)\]\[(.*)\]\[(.*)\](.*)\"|iU', $form, $fields);

				if (isset($fields[5]) AND count($fields[5]) > 0)
				{
					$count = count($fields[3]);

					for ($i = 0; $i < $count; $i++)
					{
						$form = str_replace($fields[0][$i], ' '.$attr.'="widget-'.$id_base.'['.$number.']['.$fields[5][$i].']'.$fields[6][$i].'"', $form);
					}
				}
			}

			return $form;
		}


		public static function get_defaults($key)
		{
			$key = ( ! is_array($key) ? array($key) : $key);

			$result = array();

			foreach ($key as $k)
			{
				$result = array_merge($result, self::$defaults[$k]);
			}

			return $result;
		}

		public static function get_data($widget, $key)
		{
			$key = ( ! is_array($key) ? array($key) : $key);

			foreach ($key as $case)
			{
				switch ($case)
				{
					case 'image_dimensions':
					{
						$widget['image_width'] = isset($widget['image_width']) ? ether::unit($widget['image_width'], 'px') : '';
						$widget['image_height'] = isset($widget['image_height']) ? ether::unit($widget['image_height'], 'px') : '';
						$widget['image_crop_width'] = isset($widget['image_crop_width']) ? ether::unit($widget['image_crop_width'], 'px') : '';
						$widget['image_crop_height'] = isset($widget['image_crop_height']) ? ether::unit($widget['image_crop_height'], 'px') : '';

						break;
					}
					case 'widget_dimensions':
					{
						$widget['width'] = isset($widget['width']) ? ether::unit($widget['width'], 'px') : '';
						
						//account for custom non numeric values such as 'auto', 'custom', 'constrain'
						if (isset($widget['height']) && is_numeric($widget['height']))
						{
							$widget['height'] = ether::unit($widget['height'], 'px');
						}

						break;
					}
				}
			}

			return $widget;
		}

		public static function append_classes($classes, $widget, $key)
		{
			$key = ( ! is_array($key) ? array($key) : $key);

			foreach ($key as $case)
			{
				switch ($case)
				{
					case 'img_title':
					{
						if (
							! empty($widget['show_img_title'])
							|| isset($widget['enable_title']) && $widget['enable_title'] == 'on' //backward compatibility
						)
						{
							// $classes[] = 'show-img-title'; //interfered with show-img-title-type because of cattr fn being inspecific

							if ($widget['show_img_title'] == 'on')
							{
								$classes[] = 'show-img-title-always';
							} else if ($widget['show_img_title'] == 'hover' || $widget['enable_title'] == 'on') //second condition for backward compatibility
							{
								$classes[] = 'show-img-title-on-hover';
							}

							if ( ! empty($widget['img_title_alignment_y']))
							{
								$classes[] = 'img-title-alignment-y-'.$widget['img_title_alignment_y'];
							}

							if ( ! empty($widget['img_title_alignment_x'])) //not used
							{
								$classes[] = 'img-title-alignment-x-'.$widget['img_title_alignment_x'];
							}
						}

						break;
					}
					case 'media_wrap':
					{
						if ($widget['height'] == 'custom' && isset($widget['custom_height']))
						{
							preg_match('/(\d+)/', $widget['custom_height'], $height);
							count($height) ? $height = $height[1] : '';
							intval($height) <= 0 ? $height = 'auto' : '';
						}


						$classes[] = 'media-height-ratio-'.$widget['ratio'];
						$classes[] = 'media-height-'.(isset($height) ? $height : $widget['height']);
						$classes[] = 'image-stretch-mode-'.$widget['image_mode'];
						$classes[] = 'image-alignment-x-'.$widget['image_alignment_x_parent'];
						$classes[] = 'image-alignment-y-'.$widget['image_alignment_y_parent'];
						( ! empty($widget['frame'])) ? $classes[] = 'frame-style-'.$widget['frame'] : '';

						break;
					}
					case 'widget_alignment':
					{
						if ( ! empty($widget['align']))
						{
							$classes[] = 'align'.$widget['align'];
						}

						break;
					}
					case 'grid_structure':
					{
						$classes[] = 'cols-'.$widget['columns'];
						$classes[] = 'rows-'.$widget['rows'];
						$classes[] = 'spacing-'.(isset($widget['disable_spacing']) && $widget['disable_spacing'] == 'on' ? 0 : 1);
						isset($widget['col_spacing_size']) && ! empty($widget['col_spacing_size']) ? $classes[] = 'spacing-size-'.$widget['col_spacing_size'] : '';

						break;
					}
					case 'widget_clearfloat':
					{
						isset($widget['clearfloat']) && $widget['clearfloat'] == 'on' ? $classes[] = 'clearfloat' : '';

						break;
					}
				}
			}

			return $classes;
		}

		protected function form_grid($widget)
		{
			$widget = ether::extend(array(
				// 'col_spacing_size' => ether::option('builder_grid_spacing')
			), $widget);

			// empty($widget['col_spacing_size']) ? $widget['col_spacing_size'] = ether::option('builder_grid_spacing') : ''; //don't force defaults so that local values are not stored upon global change

			$columns = array();
			$rows = array();

			for ($i = 1; $i <= 10; $i++)
			{
				if ($i == 7 || $i == 9)
				{
					$i++;
				}

				$columns[$i] = $i;
				$rows[$i] = $i;
			}

			$output = '';
			$output .= '<h2 class="ether-tab-title">'.ether::langr('Grid Settings').'</h2>
				<div class="ether-tab-content">
					<div class="cols cols-3">
						<div class="col">
							<label><span class="label-title">'.ether::langr('Columns').'</span> '.$this->field('select', 'columns', $widget, array('options' => $columns, 'class' => 'ether-cond-field ether-field-column-count')).'</label>
						</div>
						<div class="col">
							<label><span class="label-title">'.ether::langr('Rows').'</span> '.$this->field('select', 'rows', $widget, array('options' => $rows, 'class' => 'ether-cond-field ether-field-row-count')).'<small>'.ether::langr('Row count applies only if element is a slider. If you want to limit element count for non slider elements set a proper \'Count\' value if available or limit the amount of included elements').'</small></label>
						</div>
					</div>
					<div class="ether-cond-group ether-action-show-ether-filter-isnot-ether-cond-1-ether-field-column-count-or-ether-filter-isnot-ether-cond-1-ether-field-row-count">
						<hr />
						<div class="">
							<div class="cols cols-3">
									<div class="col">
								<label>'.$this->field('checkbox', 'disable_spacing', $widget, array('class' => 'ether-cond-field ether-field-disable-spacing')).'<span class="label-title"> '.ether::langr('Disable spacing').'</span> <small>'.ether::langr('Most useful for galleries with images/videos where you don\'t want spacing between elements to occur').'</small></label>
								</div>
								<div class="col ether-cond-group ether-action-show-ether-cond-off-ether-field-disable-spacing">
									<label>
										<span class="label-title">'.ether::langr('Grid spacing size').'</span> '.$this->field('text', 'col_spacing_size', $widget).'<small>'.ether::langr('Spacing between columns and rows. Default value ('.ether::option('builder_grid_spacing').') will be used if left blank').'</small>
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>';

			return $output;
		}

		protected function form_single_image_title($widget)
		{
			$title_display_options = array
			(
				'' => ether::langr('Don\'t show'),
				'on' => ether::langr('Always show'),
				'hover' => ether::langr('Show on mouse hover')
			);

			$img_title_alignment_y = array
			(
				'bottom' => ether::langr('Bottom (Default)'),
				'top' => ether::langr('Top')
			);


			$output = '';

			$output .= '
				<div class="cols cols-3">
					<div class="col">
						<label><span class="label-title">'.ether::langr('Image Title').' <abbr title="required">*</abbr></span> '.$this->field('text', 'description', $widget, array('class' => 'ether-cond-field ether-field-image-widget-image-title')).'</label>
					</div>
					<div class="col ether-cond-group ether-action-show-ether-filter-isnot-ether-cond--ether-field-image-widget-image-title">
						<label><span class="label-title">'.ether::langr('Title display options').'</span> '.$this->field('select', 'show_img_title', $widget, array('options' => $title_display_options, 'class' => 'ether-cond-field ether-field-image-widget-show-image-title')).'</label>
					</div>
					<div class="col ether-cond-group ether-action-show-ether-filter-isnot-ether-cond--ether-field-image-widget-image-title-and-ether-filter-isnot-ether-cond--ether-field-image-widget-show-image-title">
						<label><span class="label-title">'.ether::langr('Title alignment').'</span> '.$this->field('select', 'img_title_alignment_y', $widget, array('options' => $img_title_alignment_y)).'</label>
					</div>
				</div>';

			return $output;
		}

		protected function form_batch_image_title($widget)
		{
			$title_display_options = array
			(
				'' => ether::langr('Don\'t show'),
				'on' => ether::langr('Always show'),
				'hover' => ether::langr('Show on mouse hover')
			);

			$img_title_alignment_y = array
			(
				'bottom' => ether::langr('Bottom (Default)'),
				'top' => ether::langr('Top')
			);
			$output = '';

			$output .= '
				<div class="cols cols-3">
					<div class="col">
						<label><span class="label-title">'.ether::langr('Title display options').'</span> '.$this->field('select', 'show_img_title', $widget, array('options' => $title_display_options, 'class' => 'ether-cond-field ether-field-image-widget-show-image-title')).'</label>
					</div>
					<div class="col ether-cond-group ether-action-show-ether-filter-isnot-ether-cond--ether-field-image-widget-show-image-title">
						<label><span class="label-title">'.ether::langr('Title alignment').'</span> '.$this->field('select', 'img_title_alignment_y', $widget, array('options' => $img_title_alignment_y)).'</label>
					</div>
				</div>';

			return $output;
		}

		//keep widgets admin form compatible with form widgets and hence ready for copy&paste without any adjustments
		protected function form_conditional($widget)
		{
			return '';
		}

		protected function form_media_frame($widget, $data = array())
		{
			$frames = array
			(
				'' => ether::langr('Theme default'),
				'1' => ether::langr('Ether frame 1'),
				'2' => ether::langr('Ether frame 2')
			);

			$height = array
			(
				'auto' => ether::langr('Default'),
				'200' => ether::langr('Short (200px)'),
				'300' => ether::langr('Medium (300px)'),
				'400' => ether::langr('Tall (400px)'),
				'constrain' => ether::langr('Constrain (max 400px)'),
				'custom' => ether::langr('Custom')
			);

			$image_mode = array
			(
				'auto' => ether::langr('Default'),
				'x' => ether::langr('Stretch X'),
				'y' => ether::langr('Stretch Y'),
				'fit' => ether::langr('Fit'),
				'fill' => ether::langr('Fill')
			);

			$image_alignment_x_parent = array
			(
				'middle' => ether::langr('Default (middle)'),
				'left' => ether::langr('Left'),
				'right' => ether::langr('Right')
			);

			$image_alignment_y_parent = array
			(
				'middle' => ether::langr('Default (middle)'),
				'top' => ether::langr('Top'),
				'bottom' => ether::langr('Bottom')
			);

			$ratio = array
			(
				50 => ether::langr('50%%'),
				75 => ether::langr('75%%'),
				100 => ether::langr('100%%'),
				150 => ether::langr('150%%'),
				200 => ether::langr('200%%')
			);

			! isset($data['append_lightbox']) ? $data['append_lightbox'] = TRUE : '';

			$output = '';

			$link_options = '';

			if ($data['append_lightbox'])
			{
				$link_options .= '
					<div class="cols cols-3">
						<div class="col">
							<label class="">'.$this->field('checkbox', 'disable_lightbox', $widget, array('class' => 'ether-cond-field ether-field-disable-lightbox-'.$this->get_slug())).' <span class="label-title">'.ether::langr('Disable lightbox').'</span></label>
						</div>
						<div class="col">
							<label class="ether-cond-group ether-action-show-ether-cond-on-ether-field-disable-lightbox-'.$this->get_slug().'">'.$this->field('checkbox', 'open_links_in_new_tab', $widget).' <span class="label-title">'.ether::langr('Open links in new tab').'</span></label>
						</div>
					</div>';
			}

			if ($data['append_link_to_post'])
			{
				$link_options .= '
					<div class="cols cols-3">
						<div class="col">
							<label class="">'.$this->field('checkbox', 'link_to_post', $widget).' <span class="label-title">'.ether::langr('Image as a link to post').'</span></label>
						</div>
					</div>';
			}

			$output .= '<div class="cols cols-2">
					<div class="col">
						<label><span class="label-title">'.ether::langr('Elements Height').'</span> '.$this->field('select', 'height', $widget, array('options' => $height, 'class' => 'ether-cond-field ether-field-media-height')).'<small>'.ether::langr('Default: Does nothing. Constrain: Constrains element container size ratio to 1:1. If you want to constrain images size instead, use crop width/height fields below').'</small></label>
						<label class="ether-cond-group ether-action-show-ether-cond-constrain-ether-field-media-height "><span class="label-title">'.ether::langr('Constrain Ratio').'</span> '.$this->field('select', 'ratio', $widget, array('options' => $ratio, 'class' => '')).'<small>'.ether::langr('Height to width ratio for constrained galleries.').'</small></label>
						<label class="ether-cond-group ether-action-show-ether-cond-custom-ether-field-media-height "><span class="label-title">'.ether::langr('Custom Height (in Pixels)').'</span> '.$this->field('input', 'custom_height', $widget).'</label>
					</div>
					<div class="col">
						<label><span class="label-title">'.ether::langr('Frame Style').'</span> '.$this->field('select', 'frame', $widget, array('options' => $frames)).'</label>
					</div>
				</div>
				<hr class="ether-divider" />
				<h3 class="ether-section-title">'.ether::langr('Preview options').'</h3>
				'.$link_options.'
				<p class="ether-info" style="clear:both;">'.ether::langr('Image Mode related settings below will only apply when Elements Height is different than Default').'</p>
				<div class="cols cols-3">
					<div class="col">
						<label><span class="label-title">'.ether::langr('Image mode').'</span> '.$this->field('select', 'image_mode', $widget, array('options' => $image_mode)).'<small>'.ether::langr('Default: No image manipulations. Fit: Images will be scaled to fit whole available space. Stretch: Stretches images horizontally/vertically. Fit: Fits image within container - will usually leave out blank spaces').'</small></label>
					</div>
					<div class="col">
						<label><span class="label-title">'.ether::langr('Image align x').'</span> '.$this->field('select', 'image_alignment_x_parent', $widget, array('options' => $image_alignment_x_parent)).'<small>'.ether::langr('Horizontal alignment of the image in relation to it\'s parent').'</small></label>
					</div>
					<div class="col">
						<label><span class="label-title">'.ether::langr('Image align y').'</span> '.$this->field('select', 'image_alignment_y_parent', $widget, array('options' => $image_alignment_y_parent)).'<small>'.ether::langr('Vertical alignment of the image in relation to it\'s parent').'</small></label>
					</div>
				</div>
				'.$this->form_batch_image_title($widget);

			return $output;
		}

		protected function form_image_dimensions($widget, $skip_scale = FALSE, $skip_crop = FALSE)
		{
			$output = '';

			$output .= '
			<hr />
			<h3 class="ether-section-title">Image dimensions and crop</h3>'
			.( ! $skip_scale ? '
			<div class="cols cols-2">
				<div class="col">
					<label><span class="label-title">'.ether::langr('Image width').'</span> '.$this->field('text', 'image_width', $widget).'<small>'.ether::langr('"0" or blank field skips scaling through image width attribute.').'</small></label>
				</div>
				<div class="col">
					<label><span class="label-title">'.ether::langr('Image height').'</span> '.$this->field('text', 'image_height', $widget).'<small>'.ether::langr('"0" or blank field skips scaling through image width attribute.').'</small></label>
				</div>
			</div>' : '')
			.( ! $skip_crop ? '
			<div class="cols-2 cols">
				<div class="col">
					<label><span class="label-title">'.ether::langr('Image crop width').'</span> '.$this->field('text', 'image_crop_width', $widget).'<small>'.ether::langr('"0" or blank field skips scaling/croping image. This function will generate thumbnail.').'</small></label>
				</div>
				<div class="col">
					<label><span class="label-title">'.ether::langr('Image crop height').'</span> '.$this->field('text', 'image_crop_height', $widget).'<small>'.ether::langr('"0" or blank field skips scaling/croping image. This function will generate thumbnail.').'</small></label>
				</div>
			</div>' : '');

			return $output;
		}

		protected function form_widget_general($widget, $height = false, $data = NULL)
		{
			$aligns = array
			(
				'' => ether::langr('Default'),
				'left' => ether::langr('Left'),
				'right' => ether::langr('Right'),
				'center' => ether::langr('Center')
			);

			$output = '';

			$output .= '<div class="cols cols-'.($height == true ? 3 : 2).'">
				<div class="col">
					<label><span class="label-title">'.ether::langr('Widget Alignment').'</span> '.$this->field('select', 'align', $widget, array('options' => $aligns, 'class' => 'builder-widget-align-field')).(isset($data) && isset($data['align']) && isset($data['align']['info']) ? '<small>'.$data['align']['info'].'</small>' : '').'</label>
				</div>
				<div class="col">
					<label><span class="label-title">'.ether::langr('Widget width').'</span> '.$this->field('text', 'width', $widget, array('class' => 'builder-widget-width-field')).(isset($data) && isset($data['width']) && isset($data['width']['info']) ? '<small>'.$data['width']['info'].'</small>' : '').'</label>
				</div>'
				.($height == true ? '<div class="col">
					<label><span class="label-title">'.ether::langr('Widget height').'</span> '.$this->field('text', 'height', $widget, array('class' => 'builder-widget-height-field')).(isset($data) && isset($data['height']) && isset($data['height']['info']) ? '<small>'.$data['height']['info'].'</small>' : '').'</label>
				</div>
				' : '').'
			</div>';

			return $output;
		}

		protected function form_widget_clearfloat($widget)
		{
			$output = '';

			$output .= '<div class="cols cols-1">
				<div class="col">
					<label><span class="label-title">'.ether::langr('Clear this widget away from left/right aligned widgets.').'</span> '.$this->field('checkbox', 'clearfloat', $widget, array('class' => 'builder-widget-clearfloat-field')).'</label>
				</div>
			</div>';

			return $output;
		}

		protected function form_widget_visibility($widget)
		{
			$output = '';

			$output .= '<div class="cols cols-1">
				<div class="col">
					<label><span class="label-title">'.ether::langr('Hide this widget.').'</span> '.$this->field('checkbox', 'is_hidden', $widget, array('class' => 'builder-widget-visibility-field')).'<small>'.ether::langr('Use this to temporarily hide this widget from the view in the front-end without removing the widget entirely').'</small></label>
				</div>
			</div>';

			return $output;
		}

		protected function is_hidden($widget)
		{
			return ! isset($widget['is_hidden']) ? false : $widget['is_hidden'] == 'on' ? true : false;
		}
	}
}

if ( ! class_exists('ether_wp_widget'))
{
	class ether_wp_widget extends ether_builder_widget
	{
		protected $wp_class;
		protected $wp_widget;

		public function widget($widget)
		{
			// fix for sidebar widgets with custom loops (if sidebar widgets has custom loop, it breaks slug generation and some other things)
			global $post;
			$tmp = $post;

			ob_start();
			$this->wp_widget->widget(array(), $widget);

			$output = '<div class="ether-widget ether-wp">'.ob_get_clean().'</div>';

			if (isset($widget['classes']) AND ! empty($widget['classes']))
			{
				$output = ether::set_attr('*', 'class', $widget['classes'], $output, TRUE);
			}

			$post = $tmp;

			return $output;
		}

		public function form($widget)
		{
			// fix for sidebar widgets with custom loops (if sidebar widgets has custom loop, it breaks slug generation and some other things)
			global $post;
			$tmp = $post;

			if (is_array($widget) AND ! empty($widget))
			{
				foreach ($widget as $key => $value)
				{
					if ($widget[$key] === 'on')
					{
						$widget[$key] = TRUE;
					}
				}
			}

			ob_start();
			$this->wp_widget->form($widget);
			$form = ob_get_clean();

			preg_match_all('| name=\"widget-(.+)\[(.*)\]\[(.*)\]\"|iU', $form, $fields);

			if (isset($fields[3]) AND count($fields[3]) > 0)
			{
				$count = count($fields[3]);

				for ($i = 0; $i < $count; $i++)
				{
					$form = str_replace($fields[0][$i], ' name="'.$this->get_field_name($fields[3][$i]).'"', $form);
				}
			}

			$post = $tmp;

			return $form.'<fieldset class="ether-form def"><label>'.ether::langr('Additional classes').' <input type="text"'.$this->get_field_atts('classes').' name="'.$this->get_field_name('classes').'" value="'.(isset($widget['classes']) ? $widget['classes'] : '').'" /></label><label>'.ether::langr('Widget label').' '.$this->field('text', 'admin-label', $widget).'<small>'.ether::langr('Custom widget label which will be shown instead of widget name in the admin view').'</small></label></fieldset>';
		}
	}
}

if ( ! class_exists('ether_builder_sidebar_widget'))
{
	class ether_builder_sidebar_widget extends WP_Widget
	{
		protected $builder_class;
		protected $builder_widget;

		public function widget($args, $instance)
		{
			if ( ! is_admin() AND did_action('ether_builder_header') == 0)
			{
				do_action('ether_builder_header');
			}

			echo $this->builder_widget->widget($instance);
		}

		public function update($new_instance, $old_instance)
		{
			return $new_instance;
		}

		public function form($instance)
		{
			echo $this->builder_widget->wp_admin_form($instance, $this->id_base, $this->number);
		}
	}
}

ether::import('modules.builder-widget');

?>

<?php

ether::import('admin.metabox.builder');

if ( ! class_exists('ether_metabox_builder_post'))
{
	class ether_metabox_builder_post extends ether_metabox
	{
		public static function init()
		{
			ether_metabox_builder::init();
		}

		public static function header()
		{
			ether_metabox_builder::header();
		}

		public static function layout_save($name, $layout)
		{
			global $post;

			update_post_meta($post->ID, ether::config('prefix').'builder_template_'.ether::slug($name), array('name' => $name, 'layout' => $layout), FALSE);
		}

		public static function layout_get($id)
		{
			global $post;
			global $wpdb;

			$layouts = self::layout_list();

			if (isset($layouts[$id]))
			{
				$layout = $wpdb->get_var($wpdb->prepare('SELECT meta_value FROM '.$wpdb->postmeta.' WHERE meta_id=%d', $id));
				$layout = unserialize($layout);

				$_POST['ether_builder_widget'] = $layout['layout'];
				ether::meta('builder_data', $layout['layout'], $post->ID, TRUE);
			}
		}

		public static function layout_load($id)
		{
			global $post;
			global $wpdb;

			$layouts = self::layout_list();

			if (isset($layouts[$id]))
			{
				$layout = $wpdb->get_var($wpdb->prepare('SELECT meta_value FROM '.$wpdb->postmeta.' WHERE meta_id=%d', $id));
				$layout = unserialize($layout);

				$_POST['ether_builder_widget'] = $layout['layout'];
				ether::meta('builder_data', $layout['layout'], $post->ID, TRUE);
			}
		}

		public static function layout_remove($id)
		{
			global $post;
			global $wpdb;

			$layouts = self::layout_list();

			if (isset($layouts[$id]))
			{
				$wpdb->query($wpdb->prepare('DELETE FROM '.$wpdb->postmeta.' WHERE meta_id=%d', $id));
			}
		}

		public static function layout_list()
		{
  			global $wpdb;

  			$layouts = $wpdb->get_results('SELECT post_id, meta_id, meta_value FROM '.$wpdb->postmeta.' WHERE meta_key LIKE \''.ether::config('prefix').'builder_template%\'');
  			$output = array();

  			foreach ($layouts as $layout)
  			{
  				$data = unserialize($layout->meta_value);

  				$output[$layout->meta_id] = array('name' => $data['name'].' (Post ID: '.$layout->post_id.')', 'post_id' => $layout->post_id);
  			}

  			return $output;
		}

		public static function save($post_id)
		{
			/*ether::handle_field($_POST, array
			(
				'checkbox' => array
				(
					array
					(
						'name' => 'template_dynamic',
						'value' => ''
					)
				)
			));*/

			global $post;
			global $post_type;

			if ($post != NULL AND ! wp_is_post_revision($post_id))
			{
				if (ether::request('builder_template_save', 'POST'))
				{
					$request = ether::get_request('builder_template_name', 'POST');

					if ( ! empty($request))
					{
						self::layout_save($request, $_POST['ether_builder_widget']);
					}
				}

				if (ether::request('builder_template_load', 'POST'))
				{
					$request = ether::get_request('builder_template', 'POST');

					if ( ! empty($request))
					{
						self::layout_load($request);
					}
				}

				if (ether::request('builder_template_remove', 'POST'))
				{
					$request = ether::get_request('builder_template', 'POST');

					if ( ! empty($request))
					{
						self::layout_remove($request);
					}
				}

				ether::handle_field($_POST, array
				(
					'checkbox' => array
					(
						array
						(
							'name' => 'featured',
							'value' => '',
							'relation' => 'meta'
						),
						array
						(
							'name' => 'preview_image',
							'value' => '',
							'relation' => 'meta'
						),
						array
						(
							'name' => 'preview_alt',
							'value' => '',
							'relation' => 'meta'
						),
						array
						(
							'name' => 'canvas',
							'value' => '',
							'relation' => 'meta'
						)
					)
				));
			}

			if ( ! ether::request('builder_template_load', 'POST'))
			{
				ether_metabox_builder::save($post_id);
			}
		}

		public static function body()
		{
			/*return '<fieldset class="ether-form def">
				<label>'.ether::make_field('template_dynamic', array('type' => 'checkbox', 'relation' => 'meta')).' '.ether::langr('Dynamic template').'</label>
			</fieldset>';*/

			$layouts = self::layout_list();

			return '<div class="builder-options">
				<p class="builder-template">'.ether::langr('Templates').': <a href="#template-load">'.ether::langr('Load').'</a> <a href="#template-save">'.ether::langr('Save').'</a></p>
				<fieldset class="ether-form def" style="display: none;">
					<label>'.ether::langr('Templates').ether::make_field('builder_template', array('type' => 'select', 'options' => $layouts)).'</label>
					<div class="buttonset-1">
						'.ether::make_field('builder_template_load', array('type' => 'submit', 'value' => ether::langr('Load'), 'class' => 'alignright')).'
						'.ether::make_field('builder_template_remove', array('type' => 'submit', 'value' => ether::langr('Remove'), 'class' => 'alignright confirm')).'
					</div>
				</fieldset>
				<fieldset class="ether-form def hide-if-no-js" style="display: none;">
					<label>'.ether::langr('Save template').' '.ether::make_field('builder_template_name', array('type' => 'text')).'</label>
					<div class="buttonset-1">
						'.ether::make_field('builder_template_save', array('type' => 'submit', 'value' => ether::langr('Save'), 'class' => 'alignright')).'
					</div>
				</fieldset>
				<p class="builder-featured">'.ether::langr('Featured').': <a href="#post-featured">'.(ether::meta('featured') == 'on' ? ether::langr('Yes') : ether::langr('No')).'</a></p>
				<fieldset class="ether-form def hide-if-no-js" style="display: none;">
					<label>'.ether::make_field('featured', array('type' => 'checkbox', 'relation' => 'meta')).' '.ether::langr('Featured').'</label>
				</fieldset>
				<p class="builder-preview">'.ether::langr('Preview image').': <a href="#post-preview">'.ether::langr('Change').'</a></p>
				<fieldset class="ether-form def hide-if-no-js" style="display: none;">
					<div class="preview-img-wrap">'.ether::make_field('preview_image', array('type' => 'image', 'class' => 'ether-preview upload_preview_image', 'relation' => 'meta', 'use_default' => TRUE, 'value' => ether::path('media/images/placeholder.png', TRUE))).'</div>
					<label>'.ether::langr('Image').' '.ether::make_field('preview_image', array('type' => 'text', 'class' => 'upload_preview_image', 'relation' => 'meta')).'</label>
					<label>'.ether::langr('"Alt" description').' '.ether::make_field('preview_alt', array('type' => 'text', 'class' => 'upload_preview_image_alt', 'relation' => 'meta')).'</label>
					<div class="buttonset-1">
						'.ether::make_field('upload_preview_image', array('type' => 'upload_image', 'single' => TRUE, 'class' => 'alignright', 'value' => ether::langr('Upload'))).'
						'.ether::make_field('remove_preview_image', array('type' => 'submit', 'class' => 'alignright remove_image', 'value' => ether::langr('Remove'))).'
					</div>
				</fieldset>
				<p class="builder-canvas">'.ether::langr('Blank canvas').': <a href="#post-canvas">'.(ether::meta('canvas') == 'on' ? ether::langr('Yes') : ether::langr('No')).'</a></p>
				<fieldset class="ether-form def hide-if-no-js" style="display: none;">
					<label>'.ether::make_field('canvas', array('type' => 'checkbox', 'relation' => 'meta')).' '.ether::langr('Blank canvas').'</label>
				</fieldset>
			</div>';

			return '<p>'.ether::langr('This page is using Content builder. Learn more %shere%s.', '<a href="http://ether-wp.com/docs/building-content/content-building-tools/content-builder">', '</a>').'</p>';
		}
	}
}

?>

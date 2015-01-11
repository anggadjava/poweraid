<?php

if ( ! class_exists('ether_panel_ether_license'))
{
	class ether_panel_ether_license extends ether_panel
	{
		public static function init()
		{

		}

		public static function header()
		{

		}

		public static function reset()
		{
			ether::handle_field_group(array(), array
			(
				'license_key' => array()
			));

			ether::handle_field_group(array(), array
			(
				'license_service' => array()
			));
		}

		public static function save()
		{
			$branches = ether::branches();

			$branches_fields = array();

			foreach ($branches as $branch_name => $branch_data)
			{
				$branches_fields[] = array
				(
					'name' => ''.$branch_name.'_key',
					'value' => ''
				);

				$branches_fields[] = array
				(
					'name' => ''.$branch_name.'_service',
					'value' => ''
				);
			}

			ether::handle_field_group($_POST, array
			(
				'license' => array_merge(array('relation' => 'option'), $branches_fields)
			));
		}

		public static function body()
		{
			$services = array
			(
				'envato' => array('name' => ether::langr('Envato')),
				'ether' => array('name' => ether::langr('Ether'))
			);

			$license = ether::option('license');

			if ( ! empty($license))
			{
				$license = $license[0];
			}

			$branches = ether::branches();

			$licenses = '';

			foreach ($branches as $branch_name => $branch_data)
			{
				$licenses .= '
				<div class="ether-box">
					<h3 class="ether-box-title">'.$branch_data['name'].'</h3>
					<div class="ether-box-inside double-spacing">
						<div class="cols cols-2">
							<div class="col">
								<label>'.$branch_data['name'].' '.ether::langr('license key').' '.ether::make_field($branch_name.'_key[]', array('type' => 'text', 'relation' => 'custom'), $license).'</label>
							</div>
							<div class="col">
								<label>'.$branch_data['name'].' '.ether::langr('license service').' '.ether::make_field($branch_name.'_service[]', array('type' => 'select', 'relation' => 'custom', 'options' => $services), $license).'</label>
							</div>
						</div>
					</div>
				</div>';
			}

			return '<fieldset class="ether-form def">
				<div class="ether-box ether-box-no-inside">
					<h2 class="ether-box-title">'.ether::langr('Ether License').'</h2>
				</div>
				<div class="ether-box">
					<h3 class="ether-box-title">Info</h3>
					<div class="ether-box-inside double-spacing">
						<p class="ether-error">'.ether::langr('If you see "invalid license key notice" after entering the key that you\'re sure is valid please redownload the most recent plugin files from CodeCanyon and update manually. If the issue persists please contact us.').'</p>
						<p class="ether-error">'.ether::langr('Due to persistent Ether > Update issues: If the auto update doesn\'t work for you despite having the proper license key provided please update manually by redownloading from CodeCanyon while we\'re getting this issue fixed! Sorry for the inconvenience.').'</p>
					</div>
				</div>
				'.$licenses.'
			</fieldset>';
		}
	}
}

?>
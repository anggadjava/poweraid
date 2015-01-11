<?php

ether::import('admin.ether-backup');

if ( ! class_exists('ether_panel_ether'))
{
	class ether_panel_ether extends ether_panel
	{
		public static function init()
		{
			if (class_exists('ether_panel_ether_backup'))
			{
				ether_panel_ether_backup::init();
			}
		}

		public static function header()
		{
			if (class_exists('ether_panel_ether_backup'))
			{
				ether_panel_ether_backup::header();
			}
		}

		public static function reset()
		{
			ether::handle_field(array(), array
			(
				'checkbox' => array
				(
					array
					(
						'name' => 'dashboard_widgets_hide_ether_board',
						'value' => ''
					),
					array
					(
						'name' => 'admin_bar_hide_ether_menu',
						'value' => ''
					)
				)
			));
		}

		public static function save()
		{
			ether::handle_field($_POST, array
			(
				'checkbox' => array
				(
					array
					(
						'name' => 'dashboard_widgets_hide_ether_board',
						'value' => ''
					),
					array
					(
						'name' => 'admin_bar_hide_ether_menu',
						'value' => ''
					)
				)
			));
		}

		public static function body()
		{
			$branches = ether::branches();
			$branches_info = '';

			foreach ($branches as $branch_name => $branch_data)
			{
				$branches_info .= '
					<li class="branch">
						<span class="branch-name">'.$branch_data['name'].' '.$branch_data['version'].'</span> '.(isset($branch_data['online_docs']) ? ' 
						<a class="ether-button-border ether-button-theme-light ether-button-with-icon ether-button-size-medium docs-icon" href="'.$branch_data['online_docs'].'"><span class="ether-button-icon"></span><span>Documentation</span></a>' : '').(isset($branch_data['online_support']) ? ' 
						<a class="ether-button-border ether-button-theme-light ether-button-with-icon ether-button-size-medium support-icon" href="'.$branch_data['online_support'].'"><span class="ether-button-icon"></span><span>Support</span></a>' : '').'
					</li> ';
			}

			$dashboard_widgets_hide_ether_board = ether::option('dashboard_widgets_hide_ether_board');
			$admin_bar_hide_ether_menu = ether::option('admin_bar_hide_ether_menu');
			$settings = '';

			$settings .= '<div class="ether-box">
				<h3 class="ether-box-title">'.ether::langr('General Settings').'</h3>
				<div class="ether-box-inside">
					<label class="ether-toggle-checkbox ether-toggle-checkbox-auto">'
						.ether::make_field('dashboard_widgets_hide_ether_board', array('type' => 'checkbox', 'relation' => 'option'), $dashboard_widgets_hide_ether_board).
						'<span class="label-title">'.ether::langr('Hide "Ether Board" widget from the Admin Dashboard Widgets').'</span>
					</label>
					<label class="ether-toggle-checkbox ether-toggle-checkbox-auto">'
						.ether::make_field('admin_bar_hide_ether_menu', array('type' => 'checkbox', 'relation' => 'option'), $admin_bar_hide_ether_menu).
						'<span class="label-title">'.ether::langr('Hide "Ether" entry from the Admin Toolbar').'</span>
					</label>
				</div>
			</div>'._n;

			return '<fieldset class="ether-form def">
				<div class="ether-box ether-box-no-inside">
					<h2 class="ether-box-title">'.ether::langr('Welcome to Ether World!').'</h2>
				</div>

				'.$settings.'
				
				<div class="ether-box">
					<h3 class="ether-box-title">'.ether::langr('Framework &amp; Plugin info').'</h3>
					<div class="ether-box-inside double-spacing">
						<ul class="ether-branches">
							<li class="branch">
								<span class="branch-name">'.ether::langr('Ether Framework').' '.ETHER_VERSION.' ('.str_replace(WP_CONTENT_DIR, '', ETHER_FILE).')</span>
							</li> '.$branches_info.'
						</ul>
					</div>
				</div>

				<div class="ether-box">
					<h3 class="ether-box-title">'.ether::langr('Need help?').'</h3>
					<div class="ether-box-inside double-spacing">
						<p>'.ether::langr('In case of a need of any further asistance that goes beyond the scope provided by plugin documentation please try the following:').'</p>
						<ul style="list-style: disc; padding-left: 20px;">
							<li>'.ether::langr('Email: <a href="mailto:contact@onether.com">contact@onether.com</a> - Note: When reporting plugin/theme incompatibility please include the following details in your message along with problem description for smoother turnaround:').'
							<ul style="list-style: circle; padding-left: 20px; padding-top: 5px;">
								<li>'.ether::langr('your item purchase code you got after purchasing an item').'</li>
								<li>'.ether::langr('zipped Plugin/Theme files so we can conduct local tests').'</li>
								<li>'.ether::langr('FTP and WP admin login/pass to your installation').'</li>
							</ul>
							</li>
						</ul>
					</div>
				</div>
			</fieldset>';
		}

		public static function use_controls()
		{
			return TRUE;
		}
	}
}

?>
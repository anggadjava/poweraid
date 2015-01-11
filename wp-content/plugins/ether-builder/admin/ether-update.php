<?php

if ( ! class_exists('ether_panel_ether_update'))
{
	class ether_panel_ether_update extends ether_panel
	{
		public static function init()
		{

		}

		public static function header()
		{
			$product = ether::option('update_product');

			$branches = ether::branches();

			$products = array();

			$license = ether::option('license');

			if (isset($license[0]))
			{
				$license = $license[0];
			}

			foreach ($branches as $branch_name => $branch_data)
			{
				$products[$branch_name] = array('name' => $branch_data['name']);
			}

			echo '<script type="text/javascript">
				(function($){$(function(){
					function update_log(message, delay, finish)
					{
						if (typeof finish == "undefined")
						{
							finish = false;
						}

						if (typeof delay == "undefined")
						{
							delay = 0;
						}

						var $line = $("<p />").html(message);

						if ( ! finish)
						{
							$line.addClass("loading");
						} else
						{
							$(window).unbind("beforeunload");
						}

						if (delay == 0)
						{
							$("div.log p.loading").removeClass("loading");
							$("div.log").append($line);
						} else
						{
							$("div.log").animate
							({
								opacity: 1
							}, delay, function()
							{
								$("div.log p.loading").removeClass("loading");
								$("div.log").append($line);
							})
						}
					}

					function update_call_server(url, data, delay, message)
					{
						if (typeof delay == "undefined")
						{
							delay = 0;
						}

						if (delay > 0)
						{
							setTimeout( function()
							{
								$.ajax({ url: "http://api.onether.com" + url, crossDomain: true, dataType: "json", data: data });

								if (typeof message != "undefined")
								{
									update_log(message);
								}
							}, delay);
						} else
						{
							$.ajax({ data: data });

							if (typeof message != "undefined")
							{
								update_log(message);
							}
						}
					}

					function update_call(data, delay, message)
					{
						if (typeof delay == "undefined")
						{
							delay = 0;
						}

						if (delay > 0)
						{
							setTimeout( function()
							{
								$.ajax({ url: "'.ether::path('ether/ether.php', TRUE).'", data: data });

								if (typeof message != "undefined")
								{
									update_log(message);
								}
							}, delay);
						} else
						{
							$.ajax({ data: data });

							if (typeof message != "undefined")
							{
								update_log(message);
							}
						}
					}

					'.(ether::request('update_do', 'POST') ? '$(window).bind("beforeunload", function(e)
					{
						return "'.ether::langr('Update in progress - do you really want to leave?').'";
					});' : '').'

					$.ajaxSetup
					({
						type: "POST",
						timeout: 180000,
						success: function(data, status)
						{
							if (typeof data == "string" || data == null)
							{
								update_log(data, 0, true);
							}

							if (typeof data.log != "undefined" && typeof data.log.length != "undefined")
							{
								for (var i = 0; i < data.log.length; i++)
								{
									update_log("<strong>" + data.log[i].message + " on line " + data.log[i].line + " in " + data.log[i].file + "</strong>", 0, true);
								}
							}

							if (data == -1)
							{
								update_log("'.ether::langr('Access denied. Aborting...').'", 0, true);
							}  else if (typeof data.error != "undefined")
							{
								update_log("<strong>" + data.message + "</strong>", 0, true);
							} else
							{
								if (typeof data.message != "undefined")
								{
									update_log(data.message);
								}

								if (typeof data.version != "undefined")
								{
									if (data.version != product_version)
									{
										update_log("'.ether::langr('New update is available').'", 0, true);
										$("button[name*=update_do]").removeClass("hide");
									} else
									{
										update_log("'.ether::langr('Everything is up to date').'", 1000, true);
									}
								}

								if (typeof data.checksums != "undefined")
								{
									update_call_server("/update", { product: license_product, service: license_service, key: license_key, checksums: data.checksums }, 1000, "'.ether::langr('Preparing update...').'");
								}

								if (typeof data.finish != "undefined")
								{
									update_log("", 1000, true);
								}

								if (typeof data.update != "undefined")
								{
									if (data.update == true)
									{
										if (typeof data.url != "undefined" && data.url != "")
										{
											update_call({ update: "download", product: product_slug, url: data.url }, 1000, "'.ether::langr('Updating...').'");
										} else
										{
											update_log("'.ether::langr('Something gone wrong, check our servers status.').'", 1000, true);
										}
									} else
									{
										update_log("", 1000, true);
									}
								}

								if (typeof data.valid != "undefined")
								{
									if (data.valid)
									{
										update_call({ update: "checksums", product: product_slug }, 1000, "'.ether::langr('Checking product filesystem...').'");
									} else
									{
										update_log("'.ether::langr('Validating license key failed.').'", 1000, true);
									}
								}
							}
						},
						error: function(xhr, status, err)
						{
							if (status !== null)
							{
								if (status == "timeout")
								{
									update_log("<strong>'.ether::langr('Connection timed out').'</strong>", 0, true);
								} else if (status == "abort")
								{
									update_log("<strong>'.ether::langr('Connection aborted').'</strong>", 0, true);
								} else
								{
									update_log("<strong>'.ether::langr('Unknown error. Aborting...').' (" + xhr.responseText + ")</strong>", 0, true);
								}
							}

							if (typeof console != \'undefined\')
							{
								console.log(xhr);
								console.log(status);
								console.log(err);
							}
						}
					});

					var product_slug = "'.ether::slug((isset($branches[$product]['name']) ? $branches[$product]['name'] : '')).'";
					var product_version = "'.(isset($branches[$product]['version']) ? $branches[$product]['version'] : '').'";
					var license_product = "'.(isset($branches[$product]['name']) ? $branches[$product]['name'] : '').'";
					var license_service = "'.$license[$product.'_service'].'";
					var license_key = "'.$license[$product.'_key'].'";

					'.(ether::request('update_do', 'POST') ?
					'update_call_server("/validate", { product: license_product, service: license_service, key: license_key }, 1000, "'.ether::langr('Validating product key...').'");':
					(ether::request('update_check', 'POST') ? 'update_call_server("/version/" + product_slug, {}, 100, "'.ether::langr('Checking for %s updates...', $branches[$product]['name']).'");' : '')).'
				});})(jQuery);
			</script>';
		}

		public static function reset()
		{

		}

		public static function save()
		{
			if ( ! ether::request('update_do', 'POST'))
			{
				ether::handle_field($_POST, array
				(
					'select' => array
					(
						array
						(
							'name' => 'update_product'
						)
					)
				));
			}
		}

		public static function body()
		{
			$product = ether::option('update_product');
			$branches = ether::branches();
			$branches_info = '';
			$products = array();

			$license = ether::option('license');

			if ( ! empty($license))
			{
				$license = $license[0];
			}

			foreach ($branches as $branch_name => $branch_data)
			{
				$branches_info .= '<li>'.$branch_data['name'].' '.$branch_data['version'].'</li>';
				$products[$branch_name] = array('name' => $branch_data['name']);
			}

			return '<fieldset class="ether-form def">
				<div class="ether-box ether-box-no-inside">
					<h2 class="ether-box-title">'.ether::langr('Ether Update').'</h2>
				</div>

					'.(( ! isset($license[$product.'_service']) OR ! isset($license[$product.'_key'])) ? '<p class="ether-warning">'.ether::langr('In order to use auto update feature you have to %sprovide license key%s for this product', '<a href="admin.php?page=ether-ether-license">', '</a>').'.</p>' : '').'
					'.('<p class="ether-warning">'.ether::langr('Due to persistent Ether > Update issues: If the auto update doesn\'t work for you despite having the proper license key provided please update manually by redownloading from CodeCanyon while we\'re getting this issue fixed! Sorry for the inconvenience').'.</p>').'
				<div class="ether-box">
					<h3 class="ether-box-title">'.ether::langr('Framework &amp; Plugin info').'</h3>
					<div class="ether-box-inside">
						<ul style="list-style: disc; padding-left: 20px;">
							<li>Ether Framework '.ETHER_VERSION.'</li> '.$branches_info.'
						</ul>
						<hr class="ether-divider">
						'.(( ! ether::request('update_do', 'POST') AND ! ether::request('update_check', 'POST')) ? '<div class="inline-labels"><label><span>'.ether::langr('Product name').': </span>'.ether::make_field('update_product', array('type' => 'select', 'options' => $products, 'style' => 'width: 200px')).'</label></div>
						<div class="buttonset-1">'.ether::make_field('update_check', array('type' => 'submit', 'class' => 'ether-button-classic', 'value' => ether::langr('Check for updates'))).'</div>' : '').'
						<div class="cols cols-1">
							<div class="col log">
							</div>
						</div>
					</div>
				</div>
				<div class="buttonset-1">'.ether::make_field('update_do', array('type' => 'submit', 'class' => 'confirm hide', 'value' => ether::langr('Update now'))).'</div>
				<input type="hidden" name="save" value="true" />
			</fieldset>';
		}
	}
}

?>
<?php

if ( ! class_exists('ether_update'))
{
	class ether_update extends ether_module
	{
		public static function init()
		{
			ether::bind('ether.post', array('ether_update', 'post'));
		}

		public static function get_path($branch)
		{
			$path = NULL;
			$branches = ether::branches();

			foreach ($branches as $branch_name => $branch_data)
			{
				if ($branch == $branch_name)
				{
					return $branch_data['app_root'];
				}
			}

			return $path;
		}

		public static function get_checksums($branch)
		{
			$path = self::get_path($branch);

			$files = ether::list_dir($path);
			$checksums = array();

			foreach ($files as $file)
			{
				$local_file = str_replace($path, '', $file);
				$checksum = md5(ether::read($file));

				$checksums[$local_file] = $checksum;
			}

			return $checksums;
		}

		public static function post($data)
		{
			if (is_user_logged_in() AND current_user_can('edit_dashboard'))
			{
				header('Content-type: text/json');

				if (isset($data['update']))
				{
					if ($data['update'] == 'checksums')
					{
						die(json_encode(array('checksums' => self::get_checksums($data['product']))));
					} else if ($data['update'] == 'download' AND isset($data['url']))
					{
						if ( ! empty($data['url']))
						{
							$path = self::download($data['url']);

							if ($path !== FALSE)
							{
								self::unpack($data['product'], $path);
								die(json_encode(array('finish' => TRUE, 'message' => 'Update has been completed.')));
							}
						}

						die(json_encode(array('finish' => TRUE, 'message' => 'Updated failed.')));
					}
				}
			} else
			{
				echo '-1';
			}
		}

		public static function download($url)
		{
			$tmp_path = tempnam('tmp', 'zip');

			$update = ether::http_get($url);

			if ( ! empty($update))
			{
				ether::write($tmp_path, $update);

				return $tmp_path;
			}

			return FALSE;
		}

		public static function unpack($branch, $source)
		{
			$path = self::get_path($branch);

			$zip = new ZipArchive();

			if ($zip->open($source) AND $path !== NULL)
			{
				$zip->extractTo($path);
				$zip->close();
			}
		}
	}
}

?>

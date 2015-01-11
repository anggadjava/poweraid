<?php

if ( ! class_exists('ether_contact'))
{
	class ether_contact extends ether_module
	{
		public static function init()
		{
			$data = ether::clean($_POST, TRUE);

			if (isset($data['contact']) AND isset($data[base64_encode('email')]))
			{
				$to = base64_decode($data[base64_encode('email')]);

				if (isset($data['contact_message']))
				{
					ether::mail(ether::config('theme_name').' '.ether::langx('Contact form', 'default title of mail send by contact form', TRUE), $data['contact_message'], $data['contact_name'], $data['contact_email'], $to);
				}
			}
		}
	}
}

?>

<?php
return [
	'id' => 'cookie-consents',
	'name' => 'Cookie Consents',
	'is_active' => true,
	'SSL_required' => false,
	'title' => 'Cookie Consents',
	'icon' => 'cookie',
	'menu_title' => 'Cookie Consents',
	'breadcrumb_title' => 'Cookie Consents',
	'order' => 0,
	'is_secret' => false,
	'layout_script_name' => '',
	'http_headers' => [
	],
	'parameters' => [
	],
	'meta_tags' => [
	],
	'contents' => [
		[
			'module_name' => 'Admin.Stats.CookieConsents',
			'controller_name' => 'Main',
			'controller_action' => 'default',
			'parameters' => [
			],
			'is_cacheable' => false,
			'output_position' => '__main__',
			'output_position_order' => 1,
		],
	],
];

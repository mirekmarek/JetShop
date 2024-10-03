<?php
return [
	'id' => 'password-reset',
	'name' => 'password-reset',
	'is_active' => true,
	'SSL_required' => false,
	'title' => 'password-reset',
	'icon' => '',
	'menu_title' => 'password-reset',
	'breadcrumb_title' => 'password-reset',
	'order' => 0,
	'is_secret' => false,
	'layout_script_name' => 'plain',
	'http_headers' => [
	],
	'parameters' => [
	],
	'meta_tags' => [
	],
	'contents' => [
		[
			'module_name' => 'Shop.Customer.PasswordReset',
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

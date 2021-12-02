<?php
return [
	'id' => 'login',
	'name' => 'Login',
	'is_active' => true,
	'SSL_required' => false,
	'title' => 'Login',
	'icon' => '',
	'menu_title' => 'Login',
	'breadcrumb_title' => 'Login',
	'is_secret' => false,
	'http_headers' => [
	],
	'layout_script_name' => 'default',
	'meta_tags' => [
	],
	'contents' => [
		[
			'module_name' => 'Shop.Login',
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

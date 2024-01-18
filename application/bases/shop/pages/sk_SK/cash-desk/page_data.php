<?php
return [
	'id' => 'cash-desk',
	'name' => 'cash desk',
	'is_active' => true,
	'SSL_required' => false,
	'title' => 'cash desk',
	'icon' => '',
	'menu_title' => 'cash desk',
	'breadcrumb_title' => 'cash desk',
	'is_secret' => false,
	'http_headers' => [
	],
	'layout_script_name' => 'cash-desk',
	'meta_tags' => [
	],
	'contents' => [
		[
			'module_name' => 'Shop.CashDesk',
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

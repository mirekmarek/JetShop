<?php
return [
	'id' => 'cash-desk',
	'name' => 'cash desk',
	'is_active' => true,
	'SSL_required' => false,
	'title' => 'Pokladna',
	'icon' => '',
	'menu_title' => 'Pokladna',
	'breadcrumb_title' => 'Pokladna',
	'order' => 0,
	'is_secret' => false,
	'layout_script_name' => 'cash-desk',
	'http_headers' => [
	],
	'parameters' => [
	],
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

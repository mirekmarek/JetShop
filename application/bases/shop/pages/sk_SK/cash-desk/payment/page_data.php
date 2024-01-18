<?php
return [
	'id' => 'cash-desk-payment',
	'name' => 'payment',
	'is_active' => true,
	'SSL_required' => false,
	'title' => 'Payment',
	'icon' => '',
	'menu_title' => '',
	'breadcrumb_title' => '',
	'is_secret' => false,
	'http_headers' => [
	],
	'layout_script_name' => 'default',
	'order' => 0,
	'meta_tags' => [
	],
	'contents' => [
		[
			'module_name' => 'Shop.CashDesk',
			'controller_name' => 'Main',
			'controller_action' => 'payment',
			'parameters' => [
			],
			'is_cacheable' => false,
			'output_position' => '__main__',
			'output_position_order' => 1,
		],
	],
];

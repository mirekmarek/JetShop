<?php
return [
	'id' => 'cash-desk-payment-notification',
	'name' => 'payment notification',
	'is_active' => true,
	'SSL_required' => false,
	'title' => 'payment notification',
	'icon' => '',
	'menu_title' => 'payment notification',
	'breadcrumb_title' => 'payment notification',
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
			'controller_action' => 'payment_notification',
			'parameters' => [
			],
			'is_cacheable' => false,
			'output_position' => '__main__',
			'output_position_order' => 1,
		],
	],
];

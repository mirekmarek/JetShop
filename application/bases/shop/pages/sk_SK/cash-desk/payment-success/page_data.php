<?php
return [
	'id' => 'cash-desk-payment-success',
	'name' => 'payment success',
	'is_active' => true,
	'SSL_required' => false,
	'title' => 'Payment success',
	'icon' => '',
	'menu_title' => 'Payment success',
	'breadcrumb_title' => 'Payment success',
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
			'controller_action' => 'payment_success',
			'parameters' => [
			],
			'is_cacheable' => false,
			'output_position' => '__main__',
			'output_position_order' => 1,
		],
	],
];

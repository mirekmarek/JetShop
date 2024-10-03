<?php
return [
	'id' => 'invoices-in-advance',
	'name' => 'Invoices in advance',
	'is_active' => true,
	'SSL_required' => false,
	'title' => 'Invoices in advance',
	'icon' => 'file-invoice',
	'menu_title' => 'Invoices in advance',
	'breadcrumb_title' => 'Invoices in advance',
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

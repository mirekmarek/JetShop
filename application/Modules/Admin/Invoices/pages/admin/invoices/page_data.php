<?php
return [
	'id' => 'invoices',
	'name' => 'Invoices',
	'is_active' => true,
	'SSL_required' => false,
	'title' => 'Invoices',
	'icon' => 'file-invoice-dollar',
	'menu_title' => 'Invoices',
	'breadcrumb_title' => 'Invoices',
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
			'module_name' => 'Admin.Invoices',
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

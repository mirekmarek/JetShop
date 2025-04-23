<?php
return [
	'id' => 'invoices-in-advance',
	'name' => 'Proforma Invoices',
	'is_active' => true,
	'SSL_required' => false,
	'title' => 'Proforma Invoices',
	'icon' => 'file-invoice',
	'menu_title' => 'Proforma Invoices',
	'breadcrumb_title' => 'Proforma Invoices',
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

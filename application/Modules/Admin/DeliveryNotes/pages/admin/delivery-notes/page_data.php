<?php
return [
	'id' => 'delivery-notes',
	'name' => 'Delivery notes',
	'is_active' => true,
	'SSL_required' => false,
	'title' => 'Delivery notes',
	'icon' => 'file-invoice',
	'menu_title' => 'Delivery notes',
	'breadcrumb_title' => 'Delivery notes',
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

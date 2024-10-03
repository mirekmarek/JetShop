<?php
return [
	'id' => 'custom-order-dispatch',
	'name' => 'Custom order dispatch',
	'is_active' => true,
	'SSL_required' => false,
	'title' => 'Custom order dispatch',
	'icon' => 'boxes-packing',
	'menu_title' => 'Custom order dispatch',
	'breadcrumb_title' => 'Custom order dispatch',
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
			'module_name' => 'Admin.OrderDispatch.CustomDispatch',
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

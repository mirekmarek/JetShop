<?php
return [
	'id' => 'stats-orders',
	'name' => 'Stats - orders',
	'is_active' => true,
	'SSL_required' => false,
	'title' => 'Stats - orders',
	'icon' => 'chart-column',
	'menu_title' => 'Stats - orders',
	'breadcrumb_title' => 'Stats - orders',
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
			'module_name' => 'Admin.Stats.Orders',
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

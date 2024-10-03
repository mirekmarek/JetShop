<?php
return [
	'id' => 'timer-manager',
	'name' => 'Timer',
	'is_active' => true,
	'SSL_required' => false,
	'title' => 'Timer',
	'icon' => '',
	'menu_title' => '',
	'breadcrumb_title' => '',
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
			'module_name' => 'Admin.TimerManager',
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

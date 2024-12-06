<?php
return [
	'id' => 'stats-views',
	'name' => 'Stats - views',
	'is_active' => true,
	'SSL_required' => false,
	'title' => 'Stats - views',
	'icon' => 'chart-column',
	'menu_title' => 'Stats - views',
	'breadcrumb_title' => 'Stats - views',
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
			'module_name' => 'Admin.Stats.Views',
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

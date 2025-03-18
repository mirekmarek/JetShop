<?php
return [
	'id' => 'jet-analytics',
	'name' => 'Jet Analytics',
	'is_active' => true,
	'SSL_required' => false,
	'title' => 'Jet Analytics',
	'icon' => 'chart-line',
	'menu_title' => 'Jet Analytics',
	'breadcrumb_title' => 'Jet Analytics',
	'order' => 0,
	'is_secret' => false,
	'layout_script_name' => 'default',
	'http_headers' => [
	],
	'parameters' => [
	],
	'definition_key' => '',
	'meta_tags' => [
	],
	'contents' => [
		[
			'module_name' => 'EShop.Analytics.Service.JetAnalytics',
			'controller_name' => 'Main',
			'controller_action' => 'default',
			'parameters' => [
			],
			'is_cacheable' => false,
			'output_position' => '__main__',
			'output_position_order' => 0,
			'manager_group' => '',
			'manager_interface' => '',
		],
	],
];

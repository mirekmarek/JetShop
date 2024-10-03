<?php
return [
	'id' => 'search',
	'name' => 'Search',
	'is_active' => true,
	'SSL_required' => false,
	'title' => 'Hledání',
	'icon' => '',
	'menu_title' => 'Hledání',
	'breadcrumb_title' => 'Hledání',
	'order' => 0,
	'is_secret' => false,
	'layout_script_name' => 'default',
	'http_headers' => [
	],
	'parameters' => [
	],
	'meta_tags' => [
	],
	'contents' => [
		[
			'module_name' => 'Shop.FulltextSearch',
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

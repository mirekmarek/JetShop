<?php
return [
	'id' => 'search-whisperer',
	'name' => 'search-whisperer',
	'is_active' => true,
	'SSL_required' => false,
	'title' => 'search-whisperer',
	'icon' => '',
	'menu_title' => 'search-whisperer',
	'breadcrumb_title' => 'search-whisperer',
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
			'module_name' => 'Admin.FulltextSearch',
			'controller_name' => 'Main',
			'controller_action' => 'whisper',
			'parameters' => [
			],
			'is_cacheable' => false,
			'output_position' => '__main__',
			'output_position_order' => 1,
		],
	],
];

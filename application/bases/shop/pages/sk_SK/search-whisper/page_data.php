<?php
return [
	'id' => 'search-whisper',
	'name' => 'search-whisper',
	'is_active' => true,
	'SSL_required' => false,
	'title' => 'search-whisper',
	'icon' => '',
	'menu_title' => 'search-whisper',
	'breadcrumb_title' => 'search-whisper',
	'order' => 0,
	'is_secret' => false,
	'layout_script_name' => 'plain',
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
			'controller_action' => 'whisper',
			'parameters' => [
			],
			'is_cacheable' => false,
			'output_position' => '__main__',
			'output_position_order' => 1,
		],
	],
];

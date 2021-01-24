<?php
return [
	'id' => '_homepage_',
	'name' => 'Homepage',
	'is_active' => true,
	'SSL_required' => false,
	'title' => 'Homepage',
	'icon' => '',
	'menu_title' => 'Homepage',
	'breadcrumb_title' => 'Homepage',
	'is_secret' => false,
	'http_headers' => [
	],
	'layout_script_name' => 'default',
	'meta_tags' => [
	],
	'contents' => [
		[
			'module_name' => 'Shop.Catalog',
			'controller_name' => 'Main',
			'controller_action' => 'homepage',
			'parameters' => [
			],
			'is_cacheable' => false,
			'output_position' => '__main__',
			'output_position_order' => 1,
		],
	],
];

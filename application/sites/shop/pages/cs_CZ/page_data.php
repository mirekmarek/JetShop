<?php
return [
	'id' => '_homepage_',
	'name' => 'Homepage',
	'is_active' => true,
	'SSL_required' => false,
	'title' => 'Hlavní stránka',
	'icon' => 'home',
	'menu_title' => 'Hlavní stránka',
	'breadcrumb_title' => 'Hlavní stránka',
	'is_secret' => false,
	'http_headers' => [
	],
	'layout_script_name' => 'default',
	'order' => 0,
	'meta_tags' => [
	],
	'contents' => [
		[
			'module_name' => 'Shop.Catalog',
			'controller_name' => 'Main',
			'controller_action' => 'default',
			'parameters' => [
			],
			'is_cacheable' => false,
			'output_position' => '__main__',
			'output_position_order' => 1,
		],
		[
			'module_name' => 'Shop.Homepage',
			'controller_name' => 'Main',
			'controller_action' => 'default',
			'parameters' => [
			],
			'is_cacheable' => false,
			'output_position' => '__main__',
			'output_position_order' => 2,
		],
	],
];

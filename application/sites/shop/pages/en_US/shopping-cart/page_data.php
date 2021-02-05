<?php
return [
	'id' => 'shopping-cart',
	'name' => 'shopping cart',
	'is_active' => true,
	'SSL_required' => false,
	'title' => 'shopping cart',
	'icon' => '',
	'menu_title' => 'shopping cart',
	'breadcrumb_title' => 'shopping cart',
	'is_secret' => false,
	'http_headers' => [
	],
	'layout_script_name' => 'shopping-cart',
	'meta_tags' => [
	],
	'contents' => [
		[
			'module_name' => 'Shop.ShoppingCart',
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

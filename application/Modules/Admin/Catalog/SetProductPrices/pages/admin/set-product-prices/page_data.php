<?php
return [
	'id' => 'set-product-prices',
	'name' => 'Set product prices',
	'is_active' => true,
	'SSL_required' => false,
	'title' => 'Set product prices',
	'icon' => 'money-bill-1',
	'menu_title' => 'Set product prices',
	'breadcrumb_title' => 'Set product prices',
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
			'module_name' => 'Admin.Catalog.SetProductPrices',
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

<?php
return [
	'id' => 'product-reviews-assessing',
	'name' => 'Product reviews assessing',
	'is_active' => true,
	'SSL_required' => false,
	'title' => 'Product reviews assessing',
	'icon' => 'check-double',
	'menu_title' => 'Product reviews assessing',
	'breadcrumb_title' => 'Product reviews assessing',
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
			'module_name' => 'Admin.Catalog.ProductReviewsAssessing',
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

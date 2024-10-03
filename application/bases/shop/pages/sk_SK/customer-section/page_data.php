<?php
return [
	'id' => 'customer-section',
	'name' => 'customer-section',
	'is_active' => true,
	'SSL_required' => false,
	'title' => 'Detail zákaznického účtu',
	'icon' => 'user',
	'menu_title' => 'Detail zákaznického účtu',
	'breadcrumb_title' => 'Detail zákaznického účtu',
	'order' => 0,
	'is_secret' => true,
	'layout_script_name' => 'default',
	'http_headers' => [
	],
	'parameters' => [
	],
	'meta_tags' => [
	],
	'contents' => [
		[
			'module_name' => 'Shop.Customer.CustomerSection',
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

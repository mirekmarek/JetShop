<?php
return [
	'id' => 'do-personal-receipt',
	'name' => 'Do Personal Receipt',
	'is_active' => true,
	'SSL_required' => false,
	'title' => 'Do Personal Receipt',
	'icon' => 'person-walking-arrow-right',
	'menu_title' => 'Do Personal Receipt',
	'breadcrumb_title' => 'Do Personal Receipt',
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
			'module_name' => 'Admin.OrderPersonalReceipt.DoPersonalReceipt',
			'controller_name' => 'Main',
			'controller_action' => 'default',
			'parameters' => [
			],
			'is_cacheable' => false,
			'output_position' => '__main__',
			'output_position_order' => 1,
			'manager_group' => '',
			'manager_interface' => '',
		],
	],
];

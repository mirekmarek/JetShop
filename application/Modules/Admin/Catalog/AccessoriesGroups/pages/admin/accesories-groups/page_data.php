<?php
return [
	'id' => 'accesories-groups',
	'name' => 'Accesories groups',
	'is_active' => true,
	'SSL_required' => false,
	'title' => 'Accesories groups',
	'icon' => 'plus',
	'menu_title' => 'Accesories groups',
	'breadcrumb_title' => 'Accesories groups',
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
			'module_name' => 'Admin.Catalog.AccessoriesGroups',
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

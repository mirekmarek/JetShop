<?php
return [
	'id' => 'entity-edit-services',
	'name' => 'Entity edit services',
	'is_active' => true,
	'SSL_required' => false,
	'title' => 'Entity edit services',
	'icon' => '',
	'menu_title' => '',
	'breadcrumb_title' => '',
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
			'module_name' => 'Admin.Entity.Edit',
			'controller_name' => 'Main',
			'controller_action' => 'default',
			'parameters' => [
			],
			'is_cacheable' => false,
			'output_position' => '__main__',
			'output_position_order' => 0,
		],
	],
];

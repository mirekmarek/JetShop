<?php
return [
	'id' => '_homepage_',
	'name' => 'Administration - homepage',
	'is_active' => true,
	'SSL_required' => false,
	'title' => 'Administration',
	'icon' => '',
	'menu_title' => 'Administration',
	'breadcrumb_title' => 'Administration',
	'is_secret' => false,
	'http_headers' => [
	],
	'layout_script_name' => 'default',
	'meta_tags' => [
	],
	'contents' => [
		[
			'module_name' => 'Admin.UI',
			'controller_name' => 'Main',
			'controller_action' => 'default',
			'parameters' => [
			],
			'output_position' => '__main__',
			'output_position_order' => 1,
		],
	],
];

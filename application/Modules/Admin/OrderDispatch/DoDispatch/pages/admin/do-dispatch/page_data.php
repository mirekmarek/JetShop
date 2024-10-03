<?php
return [
	'id' => 'do-dispatch',
	'name' => 'Do dispatch',
	'is_active' => true,
	'SSL_required' => false,
	'title' => 'Do dispatch',
	'icon' => 'boxes-packing',
	'menu_title' => 'Do dispatch',
	'breadcrumb_title' => 'Do dispatch',
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
			'module_name' => 'Admin.OrderDispatch.DoDispatch',
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

<?php
return [
	'id' => 'dictionaries-manager',
	'name' => 'Dictionaries manager',
	'is_active' => true,
	'SSL_required' => false,
	'title' => 'Dictionaries manager',
	'icon' => 'language',
	'menu_title' => 'Dictionaries manager',
	'breadcrumb_title' => 'Dictionaries manager',
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
			'module_name' => 'Admin.DictionariesManager',
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

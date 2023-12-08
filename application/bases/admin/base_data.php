<?php
return [
	'id' => 'admin',
	'name' => 'Administration',
	'base_path' => NULL,
	'layouts_path' => NULL,
	'views_path' => NULL,
	'is_secret' => true,
	'is_default' => false,
	'is_active' => true,
	'SSL_required' => false,
	'localized_data' => [
		'cs_CZ' => [
			'is_active' => true,
			'SSL_required' => false,
			'title' => 'PHP Jet Ukázková administrace',
			'URLs' => [
				'jet-shop.lc/admin/',
			],
			'default_meta_tags' => [
			],
		],
	],
	'initializer' => [
		'JetApplication\\Application_Admin',
		'init',
	],
];

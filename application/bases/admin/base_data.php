<?php
return [
	'id' => 'admin',
	'name' => 'Administration',
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
			'parameters' => [
			],
		],
		'en_US' => [
			'is_active' => true,
			'SSL_required' => false,
			'title' => '',
			'URLs' => [
				'jet-shop.lc/admin/en',
			],
			'default_meta_tags' => [
			],
			'parameters' => [
			],
		],
	],
	'initializer' => [
		'JetApplication\\Application_Admin',
		'init',
	],
];

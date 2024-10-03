<?php
return [
	'id' => 'exports',
	'name' => 'Exports',
	'is_secret' => false,
	'is_default' => false,
	'is_active' => true,
	'SSL_required' => false,
	'localized_data' => [
		'en_US' => [
			'is_active' => true,
			'SSL_required' => false,
			'title' => '',
			'URLs' => [
				'jet-shop.lc/exports/',
			],
			'default_meta_tags' => [
			],
			'parameters' => [
			],
		],
	],
	'initializer' => [
		'JetApplication\\Application_Exports',
		'init',
	],
];

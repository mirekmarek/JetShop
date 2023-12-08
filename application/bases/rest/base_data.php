<?php
return [
	'id' => 'rest',
	'name' => 'REST API',
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
			'title' => 'PHP Jet Ukázkové REST API',
			'URLs' => [
				'jet-shop.lc/rest/',
			],
			'default_meta_tags' => [
			],
		],
	],
	'initializer' => [
		'JetApplication\\Application_REST',
		'init',
	],
];

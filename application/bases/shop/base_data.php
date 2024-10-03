<?php
return [
	'id' => 'shop',
	'name' => 'Shop',
	'is_secret' => false,
	'is_default' => true,
	'is_active' => true,
	'SSL_required' => true,
	'localized_data' => [
		'cs_CZ' => [
			'is_active' => true,
			'SSL_required' => false,
			'title' => 'JetShop CS',
			'URLs' => [
				'jet-shop.lc/',
			],
			'default_meta_tags' => [
			],
			'parameters' => [
				'shop_code' => 'cz',
				'shop_name' => 'CZ shop',
				'is_default_shop' => true,
				'pricelist_codes' => 'default_czk',
				'default_pricelist_code' => 'default_czk',
				'availability_codes' => 'default_cz',
				'default_availability_code' => 'default_cz',
				'use_template' => false,
				'template_relative_dir' => 'default',
				'default_warehouse_id' => '1',
			],
		],
		'sk_SK' => [
			'is_active' => true,
			'SSL_required' => false,
			'title' => 'JetShop SK',
			'URLs' => [
				'sk.jet-shop.lc/',
			],
			'default_meta_tags' => [
			],
			'parameters' => [
				'shop_code' => 'sk',
				'shop_name' => 'SK shop',
				'pricelist_codes' => 'default_eur',
				'default_pricelist_code' => 'default_eur',
				'availability_codes' => 'default_sk',
				'default_availability_code' => 'default_sk',
				'use_template' => false,
				'template_relative_dir' => 'default',
				'is_default_shop' => false,
				'default_warehouse_id' => '2',
			],
		],
		'de_DE' => [
			'is_active' => true,
			'SSL_required' => false,
			'title' => '',
			'URLs' => [
				'de.jet-shop.lc/',
			],
			'default_meta_tags' => [
			],
			'parameters' => [
				'shop_code' => 'sk',
				'shop_name' => 'DE shop',
				'is_default_shop' => false,
				'pricelist_codes' => 'default_eur',
				'default_pricelist_code' => 'default_eur',
				'availability_codes' => 'default_sk',
				'default_availability_code' => 'default_sk',
				'use_template' => false,
				'template_relative_dir' => 'default',
				'default_warehouse_id' => '1',
			],
		],
		'hu_HU' => [
			'is_active' => true,
			'SSL_required' => false,
			'title' => '',
			'URLs' => [
				'hu.jet-shop.lc/',
			],
			'default_meta_tags' => [
			],
			'parameters' => [
				'shop_code' => 'sk',
				'shop_name' => 'HU shop',
				'is_default_shop' => false,
				'pricelist_codes' => 'default_eur',
				'default_pricelist_code' => 'default_eur',
				'availability_codes' => 'default_sk',
				'default_availability_code' => 'default_cz',
				'use_template' => false,
				'template_relative_dir' => 'default',
				'default_warehouse_id' => '2',
			],
		],
		'pl_PL' => [
			'is_active' => false,
			'SSL_required' => false,
			'title' => '',
			'URLs' => [
				'pl.jet-shop.lc/',
			],
			'default_meta_tags' => [
			],
			'parameters' => [
				'shop_code' => 'sk',
				'shop_name' => 'PL',
				'is_default_shop' => false,
				'pricelist_codes' => 'PL',
				'default_pricelist_code' => 'PL',
				'availability_codes' => 'PL',
				'default_availability_code' => 'PL',
				'default_warehouse_id' => '1',
				'use_template' => true,
				'template_relative_dir' => 'default',
			],
		],
	],
	'initializer' => [
		'JetApplication\\Application_Shop',
		'init',
	],
];

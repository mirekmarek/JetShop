<?php
return [
	'id' => 'shop',
	'name' => 'Shop',
	'base_path' => NULL,
	'layouts_path' => NULL,
	'views_path' => NULL,
	'is_secret' => false,
	'is_default' => true,
	'is_active' => true,
	'SSL_required' => false,
	'localized_data' => [
		'cs_CZ' => [
			'is_active' => true,
			'SSL_required' => false,
			'title' => 'JetShop CS',
			'URLs' => [
				'jet-shop.lc/',
			],
			'default_meta_tags' => [
				[
					'attribute' => 'aaa',
					'attribute_value' => 'bbb',
					'content' => 'ccc',
				],
			],
			'parameters' => [
				'shop_code'    => 'cz',
				'shop_name'    => 'CZ shop',

				'is_default_shop' => true,


				'currency_code'                => 'CZK',
				'currency_symbol_left'         => '',
				'currency_symbol_right'        => ',- Kč',
				'currency_with_vat_txt'        => ',- Kč vč.DPH',
				'currency_wo_vat_txt'          => ',- Kč bez DPH',
				'currency_decimal_separator'   => ',',
				'currency_thousands_separator' => ' ',
				'currency_decimal_places'      => 0,


				'phone_validation_reg_exp' => '/^([0-9]{9})$/',
				'phone_prefix'             => '+420',

				'round_precision_without_VAT' => 3,
				'round_precision_VAT'         => 2,
				'round_precision_with_VAT'    => 0,

				'vat_rates'        => '0,10,15,21',
				'default_vat_rate' => 21,

			]
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
				'shop_code'    => 'sk',
				'shop_name'    => 'SK shop',

				'currency_code'                => 'EUR',
				'currency_symbol_left'         => '',
				'currency_symbol_right'        => ' €',
				'currency_with_vat_txt'        => '€ s DP',
				'currency_wo_vat_txt'          => '€ bez DPH',
				'currency_decimal_separator'   => ',',
				'currency_thousands_separator' => ' ',
				'currency_decimal_places'      => 2,


				'phone_validation_reg_exp' => '/^([0-9]{9})$/',
				'phone_prefix'             => '+007',

				'round_precision_without_VAT' => 2,
				'round_precision_VAT'         => 2,
				'round_precision_with_VAT'    => 1,

				'vat_rates'        => '0,10,20',
				'default_vat_rate' => 20,

			]
		],
	],
	'initializer' => [
		'JetApplication\\Application_Shop',
		'init',
	],
];

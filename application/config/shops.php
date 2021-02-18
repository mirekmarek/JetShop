<?php
return [
	'shops' => [
		[
			'code'    => 'cz',
			'site_id' => 'shop',
			'locale'  => 'cs_CZ',
			'name'    => 'CZ shop',

			'is_default' => true,


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

			'vat_rates'        => [
				0,
				10,
				15,
				21
			],
			'default_vat_rate' => 21,
		],

		[
			'code'    => 'en',
			'site_id' => 'shop',
			'locale'  => 'en_US',
			'name'    => 'EN shop',

			'currency_code'                => 'EUR',
			'currency_symbol_left'         => '',
			'currency_symbol_right'        => ' €',
			'currency_with_vat_txt'        => '€ with VAT',
			'currency_wo_vat_txt'          => '€ without VAT',
			'currency_decimal_separator'   => ',',
			'currency_thousands_separator' => ' ',
			'currency_decimal_places'      => 2,


			'phone_validation_reg_exp' => '/^([0-9]{9})$/',
			'phone_prefix'             => '+007',

			'round_precision_without_VAT' => 3,
			'round_precision_VAT'         => 2,
			'round_precision_with_VAT'    => 2,

			'vat_rates'        => [
				0,
				10,
				20,
			],
			'default_vat_rate' => 20,
		],

	]
];
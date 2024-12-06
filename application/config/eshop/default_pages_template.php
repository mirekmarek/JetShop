<?php

use Jet\MVC;
use JetApplication\EShop_Managers_Articles;
use JetApplication\EShop_Managers_CashDesk;
use JetApplication\EShop_Managers_Catalog;
use JetApplication\EShop_Managers_Compare;
use JetApplication\EShop_Managers_CustomerLogin;
use JetApplication\EShop_Managers_CustomerPasswordReset;
use JetApplication\EShop_Managers_CustomerSection;
use JetApplication\EShop_Managers_FulltextSearch;
use JetApplication\EShop_Managers_OAuth;
use JetApplication\EShop_Managers_ShoppingCart;
use JetApplication\EShop_Managers_Wishlist;

return [
	'homepage' => [
		'id' => MVC::HOMEPAGE_ID,
		'URI_path_fragment' => '',
		'name' => 'Homepage',
		'title' => 'Homepage',
		'content' => [
			[
				'manager_interface' => EShop_Managers_Catalog::class,
				'controller_name' => 'Main',
				'controller_action' => 'homepage',
				'output_position_order' => 1,
			],
			[
				'module_name' => 'EShop.Marketing.LandingPage',
				'output_position_order' => 2,
			],
			[
				'manager_interface' => EShop_Managers_Articles::class,
				'output_position_order' => 3,
			],
		]
	],
	
	'cash_desk' => [
		'parent_page_key' => 'homepage',
		
		'id' => 'cash-desk',
		'URI_path_fragment' => 'cash-desk',
		'name' => 'Cash Desk',
		'title' => 'Cash Desk',
		
		'layout_script_name' => 'cash-desk',
		
		'content' => [
			[
				'manager_interface' => EShop_Managers_CashDesk::class,
			],
		]
	],
	'cash_desk_confirmation' => [
		'parent_page_key' => 'cash_desk',
		
		'id' => 'cash-desk-confirmation',
		'URI_path_fragment' => 'confirmation',
		'name' => 'Cash Desk - order confirmation',
		'title' => 'Cash Desk - confirmation',
		
		'layout_script_name' => 'cash-desk',
		
		'content' => [
			[
				'manager_interface' => EShop_Managers_CashDesk::class,
				'controller_action' => 'confirmation',
			],
		]
	
	],
	'cash_desk_payment' => [
		'parent_page_key' => 'cash_desk',
		
		'id' => 'cash-desk-payment',
		'URI_path_fragment' => 'payment',
		'name' => 'Cash Desk - payment',
		'title' => 'Cash Desk - payment',
		
		'layout_script_name' => 'cash-desk',
		
		'content' => [
			[
				'manager_interface' => EShop_Managers_CashDesk::class,
				'controller_action' => 'payment',
			],
		]
	
	],
	
	'password_reset' => [
		'parent_page_key' => 'homepage',
		
		'id' => 'password-reset',
		'URI_path_fragment' => 'password-reset',
		'name' => 'Reset password',
		'title' => 'Reset password',
		
		'layout_script_name' => 'plain',
		
		'content' => [
			[
				'manager_interface' => EShop_Managers_CustomerPasswordReset::class,
			],
		]
	
	],
	'login' => [
		'parent_page_key' => 'homepage',
		
		'id' => 'login',
		'URI_path_fragment' => 'login',
		'name' => 'Login',
		'title' => 'Login',
		
		'content' => [
			[
				'manager_interface' => EShop_Managers_CustomerLogin::class,
			],
		]
	
	],
	'customer_section' => [
		'parent_page_key' => 'homepage',
		
		'id' => 'customer-section',
		'URI_path_fragment' => 'customer-section',
		'name' => 'Customer section',
		'title' => 'Customer section',
		
		'content' => [
			[
				'manager_interface' => EShop_Managers_CustomerSection::class,
			],
		]
	
	],
	
	
	'shopping_cart' => [
		'parent_page_key' => 'homepage',
		
		'id' => 'shopping-cart',
		'URI_path_fragment' => 'shopping-cart',
		'name' => 'Shopping cart',
		'title' => 'Shopping cart',
		
		'layout_script_name' => 'shopping-cart',
		
		'content' => [
			[
				'manager_interface' => EShop_Managers_ShoppingCart::class,
			],
		]
	
	],
	'search' => [
		'parent_page_key' => 'homepage',
		
		'id' => 'search',
		'URI_path_fragment' => 'search',
		'name' => 'Search',
		'title' => 'Search',
		
		'content' => [
			[
				'manager_interface' => EShop_Managers_FulltextSearch::class,
			],
		]
	
	],
	'search_whisper' => [
		'parent_page_key' => 'homepage',
		
		'id' => 'search-whisper',
		'URI_path_fragment' => 'search-whisper',
		'name' => 'Search - whisper',
		'title' => 'Search - whisper',
		
		'layout_script_name' => 'plain',
		
		'content' => [
			[
				'manager_interface' => EShop_Managers_FulltextSearch::class,
				'controller_action' => 'whisper',
			],
		]
	
	],
	
	'compare' => [
		'parent_page_key' => 'homepage',
		
		'id' => 'compare',
		'URI_path_fragment' => 'compare',
		'name' => 'Compare',
		'title' => 'Compare',
		
		'content' => [
			[
				'manager_interface' => EShop_Managers_Compare::class,
			],
		]
	
	],
	'wishlist' => [
		'parent_page_key' => 'homepage',
		
		'id' => 'wishlist',
		'URI_path_fragment' => 'wishlist',
		'name' => 'Wishlist',
		'title' => 'Wishlist',
		
		'content' => [
			[
				'manager_interface' => EShop_Managers_Wishlist::class,
			],
		]
	
	],
	'oauth' => [
		'parent_page_key' => 'homepage',
		
		'id' => 'o-auth',
		'URI_path_fragment' => 'o-auth',
		'name' => 'o-auth service',
		'title' => 'o-auth service',
		
		'content' => [
			[
				'manager_interface' => EShop_Managers_OAuth::class
			],
		]
	
	],
	
	
	'complaints' => [
		'parent_page_key' => 'homepage',
		
		'id' => 'complaints',
		'URI_path_fragment' => 'complaints',
		'name' => 'Complaints',
		'title' => 'Complaints',
		
		'content' => [
			[
				//TODO:
				'module_name' => 'EShop.Complaints',
			],
		]
	
	],
	'return_of_goods' => [
		'parent_page_key' => 'homepage',
		
		'id' => 'return-of-goods',
		'URI_path_fragment' => 'return-of-goods',
		'name' => 'Return of goods',
		'title' => 'Return of goods',
		
		'content' => [
			[
				//TODO:
				'module_name' => 'EShop.ReturnsOfGoods',
			],
		]
	
	],

	
	'change_password' => [
		'parent_page_key' => 'homepage',
		
		'id' => 'change-password',
		'URI_path_fragment' => 'change-password',
		'name' => 'Change password',
		'title' => 'Change password',
		
		'content' => [
			//TODO:
		]
	
	],
	'sign_up' => [
		'parent_page_key' => 'homepage',
		
		'id' => 'sign-up',
		'URI_path_fragment' => 'sign-up',
		'name' => 'Sign Up',
		'title' => 'Sign Up',
		
		'content' => [
			//TODO:
		]
	
	],
	
];
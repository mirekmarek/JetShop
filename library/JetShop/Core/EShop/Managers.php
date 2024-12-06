<?php
namespace JetShop;

use Jet\Application_Module;
use JetApplication\EShopConfig;
use JetApplication\Manager_MetaInfo;
use JetApplication\Managers;
use JetApplication\EShop_Managers_Analytics;
use JetApplication\EShop_Managers_Banners;
use JetApplication\EShop_Managers_Image;
use JetApplication\EShop_Managers_PriceFormatter;
use JetApplication\EShop_Managers_ShoppingCart;
use JetApplication\EShop_Managers_CashDesk;
use JetApplication\EShop_Managers_FulltextSearch;
use JetApplication\EShop_Managers_ProductListing;
use JetApplication\EShop_Managers_CustomerLogin;
use JetApplication\EShop_Managers_CustomerPasswordReset;
use JetApplication\EShop_Managers_AutoOffers;
use JetApplication\EShop_Managers_Compare;
use JetApplication\EShop_Managers_Wishlist;
use JetApplication\EShop_Managers_Catalog;
use JetApplication\EShop_Managers_ProductReviews;
use JetApplication\EShop_Managers_ProductQuestions;
use JetApplication\EShop_Managers_PromoAreas;
use JetApplication\EShop_Managers_Articles;
use JetApplication\EShop_Managers_UI;
use JetApplication\EShop_Managers_OAuth;
use JetApplication\EShop_Managers_MagicTags;
use JetApplication\EShop_Managers_CustomerSection;

use JetApplication\EShop_CookieSettings_Manager;
use JetApplication\EShops;
use JetApplication\EShop;

class Core_EShop_Managers extends Managers
{
	/**
	 * @var Manager_MetaInfo[]|null
	 */
	protected static ?array $managers_meta_info = null;
	
	protected static ?array $config = null;
	
	protected static array $managers = [];
	
	protected static ?EShop $eshop = null;
	
	public static function getEshop(): ?EShop
	{
		if(!static::$eshop) {
			static::$eshop = EShops::getCurrent();
		}
		return static::$eshop;
	}
	
	public static function setEshop( EShop $eshop ): void
	{
		static::$eshop = $eshop;
		static::$config = null;
		static::$managers = [];
	}
	
	
	
	public static function getCfgFilePath() : string
	{
		return EShopConfig::getRootDir().'managers/shop/'.static::getEshop()->getKey().'.php';
	}
	
	protected static function registerManagers() : void
	{

		static::registerManager(
			interface_class_name: EShop_Managers_Image::class,
			is_mandatory: true,
			name: 'Images',
			description: '',
			module_name_prefix: 'EShop.'
		);
		
		static::registerManager(
			interface_class_name: EShop_Managers_PriceFormatter::class,
			is_mandatory: true,
			name: 'Price formatter',
			description: '',
			module_name_prefix: 'EShop.'
		);
		
		static::registerManager(
			interface_class_name: EShop_Managers_ShoppingCart::class,
			is_mandatory: true,
			name: 'Shopping Cart',
			description: '',
			module_name_prefix: 'EShop.'
		);
		
		static::registerManager(
			interface_class_name: EShop_Managers_CashDesk::class,
			is_mandatory: true,
			name: 'Cash Desk',
			description: '',
			module_name_prefix: 'EShop.'
		);
		
		static::registerManager(
			interface_class_name: EShop_Managers_FulltextSearch::class,
			is_mandatory: true,
			name: 'Fulltext Search',
			description: '',
			module_name_prefix: 'EShop.'
		);
		
		static::registerManager(
			interface_class_name: EShop_Managers_ProductListing::class,
			is_mandatory: true,
			name: 'Product Listing',
			description: '',
			module_name_prefix: 'EShop.'
		);
		
		static::registerManager(
			interface_class_name: EShop_Managers_CustomerLogin::class,
			is_mandatory: true,
			name: 'Customer Login',
			description: '',
			module_name_prefix: 'EShop.'
		);
		
		static::registerManager(
			interface_class_name: EShop_Managers_CustomerPasswordReset::class,
			is_mandatory: true,
			name: 'Customer Password Reset',
			description: '',
			module_name_prefix: 'EShop.'
		);
		
		static::registerManager(
			interface_class_name: EShop_Managers_UI::class,
			is_mandatory: true,
			name: 'UI',
			description: '',
			module_name_prefix: 'EShop.'
		);
		
		
		static::registerManager(
			interface_class_name: EShop_Managers_AutoOffers::class,
			is_mandatory: false,
			name: 'Auto Offers',
			description: '',
			module_name_prefix: 'EShop.'
		);
		
		static::registerManager(
			interface_class_name: EShop_Managers_Compare::class,
			is_mandatory: false,
			name: 'Compare products',
			description: '',
			module_name_prefix: 'EShop.'
		);
		
		static::registerManager(
			interface_class_name: EShop_Managers_Wishlist::class,
			is_mandatory: false,
			name: 'Wishlist',
			description: '',
			module_name_prefix: 'EShop.'
		);
		
		static::registerManager(
			interface_class_name: EShop_Managers_Banners::class,
			is_mandatory: false,
			name: 'Banners',
			description: '',
			module_name_prefix: 'EShop.'
		);
		
		static::registerManager(
			interface_class_name: EShop_Managers_Catalog::class,
			is_mandatory: false,
			name: 'Catalog',
			description: '',
			module_name_prefix: 'EShop.'
		);
		
		static::registerManager(
			interface_class_name: EShop_Managers_OAuth::class,
			is_mandatory: false,
			name: 'OAuth',
			description: '',
			module_name_prefix: 'EShop.'
		);
		
		static::registerManager(
			interface_class_name: EShop_Managers_ProductReviews::class,
			is_mandatory: false,
			name: 'Product reviews',
			description: '',
			module_name_prefix: 'EShop.'
		);
		
		static::registerManager(
			interface_class_name: EShop_Managers_ProductQuestions::class,
			is_mandatory: false,
			name: 'Product questions',
			description: '',
			module_name_prefix: 'EShop.'
		);
		
		static::registerManager(
			interface_class_name: EShop_Managers_PromoAreas::class,
			is_mandatory: false,
			name: 'Promo areas',
			description: '',
			module_name_prefix: 'EShop.'
		);
		
		static::registerManager(
			interface_class_name: EShop_Managers_Articles::class,
			is_mandatory: false,
			name: 'Articles',
			description: '',
			module_name_prefix: 'EShop.'
		);
		
		static::registerManager(
			interface_class_name: EShop_Managers_MagicTags::class,
			is_mandatory: false,
			name: 'Magic tags',
			description: '',
			module_name_prefix: 'EShop.'
		);
		
		static::registerManager(
			interface_class_name: EShop_Managers_Analytics::class,
			is_mandatory: false,
			name: 'Analytics',
			description: '',
			module_name_prefix: 'EShop.'
		);
		
		
		static::registerManager(
			interface_class_name: EShop_CookieSettings_Manager::class,
			is_mandatory: false,
			name: 'Cookie Settings',
			description: '',
			module_name_prefix: 'EShop.'
		);
		
		static::registerManager(
			interface_class_name: EShop_Managers_CustomerSection::class,
			is_mandatory: false,
			name: 'Customer Section',
			description: '',
			module_name_prefix: 'EShop.'
		);
		
		
	}
	
	
	public static function Image() : EShop_Managers_Image|Application_Module
	{
		return static::get( EShop_Managers_Image::class );
	}
	
	public static function PriceFormatter() : EShop_Managers_PriceFormatter|Application_Module
	{
		return static::get( EShop_Managers_PriceFormatter::class );
	}
	
	public static function ShoppingCart() : EShop_Managers_ShoppingCart|Application_Module
	{
		return static::get( EShop_Managers_ShoppingCart::class );
	}
	
	public static function CashDesk() : EShop_Managers_CashDesk|Application_Module
	{
		return static::get( EShop_Managers_CashDesk::class );
	}
	
	public static function FulltextSearch() : EShop_Managers_FulltextSearch|Application_Module
	{
		return static::get( EShop_Managers_FulltextSearch::class );
	}
	
	public static function ProductListing() : EShop_Managers_ProductListing|Application_Module
	{
		return static::get( EShop_Managers_ProductListing::class );
	}
	
	public static function CustomerLogin() : EShop_Managers_CustomerLogin|Application_Module
	{
		return static::get( EShop_Managers_CustomerLogin::class );
	}
	
	public static function CustomerPasswordReset() : EShop_Managers_CustomerPasswordReset|Application_Module
	{
		return static::get( EShop_Managers_CustomerPasswordReset::class );
	}
	
	public static function AutoOffers() : EShop_Managers_AutoOffers|Application_Module|null
	{
		return static::get( EShop_Managers_AutoOffers::class );
	}
	
	
	public static function Compare() : EShop_Managers_Compare|Application_Module|null
	{
		return static::get( EShop_Managers_Compare::class );
	}
	
	public static function Wishlist() : EShop_Managers_Wishlist|Application_Module|null
	{
		return static::get( EShop_Managers_Wishlist::class );
	}
	
	public static function Catalog() : EShop_Managers_Catalog|Application_Module|null
	{
		return static::get( EShop_Managers_Catalog::class );
	}
	
	
	public static function OAuth() : EShop_Managers_OAuth|Application_Module|null
	{
		return static::get( EShop_Managers_OAuth::class );
	}
	
	public static function ProductReviews() : EShop_Managers_ProductReviews|Application_Module|null
	{
		return static::get( EShop_Managers_ProductReviews::class );
	}
	
	public static function ProductQuestions() : EShop_Managers_ProductQuestions|Application_Module|null
	{
		return static::get( EShop_Managers_ProductQuestions::class );
	}
	
	public static function CookieSettings() : EShop_CookieSettings_Manager|Application_Module|null
	{
		return static::get( EShop_CookieSettings_Manager::class );
	}
	
	public static function PromoAreas() : EShop_Managers_PromoAreas|Application_Module|null
	{
		return static::get( EShop_Managers_PromoAreas::class );
	}
	
	public static function Banners() : EShop_Managers_Banners|Application_Module|null
	{
		return static::get( EShop_Managers_Banners::class );
	}
	
	public static function Articles() : EShop_Managers_Articles|Application_Module|null
	{
		return static::get( EShop_Managers_Articles::class );
	}
	
	public static function MagicTags() : EShop_Managers_MagicTags|Application_Module|null
	{
		return static::get( EShop_Managers_MagicTags::class );
	}
	
	public static function Analytics() : EShop_Managers_Analytics|Application_Module|null
	{
		return static::get( EShop_Managers_Analytics::class );
	}
	
	public static function UI() : EShop_Managers_UI|Application_Module
	{
		return static::get( EShop_Managers_UI::class );
	}
	
	public static function CustomerSection() : EShop_Managers_CustomerSection|Application_Module
	{
		return static::get( EShop_Managers_CustomerSection::class );
	}


	
}
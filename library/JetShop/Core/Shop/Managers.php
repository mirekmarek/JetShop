<?php
namespace JetShop;

use Jet\Application_Module;
use Jet\SysConf_Path;
use JetApplication\Manager_MetaInfo;
use JetApplication\Managers;
use JetApplication\Shop_Managers_Analytics;
use JetApplication\Shop_Managers_Banners;
use JetApplication\Shop_Managers_Image;
use JetApplication\Shop_Managers_PriceFormatter;
use JetApplication\Shop_Managers_ShoppingCart;
use JetApplication\Shop_Managers_CashDesk;
use JetApplication\Shop_Managers_FulltextSearch;
use JetApplication\Shop_Managers_ProductListing;
use JetApplication\Shop_Managers_CustomerLogin;
use JetApplication\Shop_Managers_CustomerPasswordReset;
use JetApplication\Shop_Managers_AutoOffers;
use JetApplication\Shop_Managers_Compare;
use JetApplication\Shop_Managers_Wishlist;
use JetApplication\Shop_Managers_Catalog;
use JetApplication\Shop_Managers_ProductReviews;
use JetApplication\Shop_Managers_ProductQuestions;
use JetApplication\Shop_Managers_PromoAreas;
use JetApplication\Shop_Managers_Articles;
use JetApplication\Shop_Managers_UI;
use JetApplication\Shop_Managers_OAuth;
use JetApplication\Shop_Managers_MagicTags;
use JetApplication\Shop_Managers_CustomerSection;

use JetApplication\Shop_CookieSettings_Manager;
use JetApplication\Shops;
use JetApplication\Shops_Shop;

class Core_Shop_Managers extends Managers
{
	/**
	 * @var Manager_MetaInfo[]|null
	 */
	protected static ?array $managers_meta_info = null;
	
	protected static ?array $config = null;
	
	protected static array $managers = [];
	
	protected static ?Shops_Shop $shop = null;
	
	public static function getShop(): ?Shops_Shop
	{
		if(!static::$shop) {
			static::$shop = Shops::getCurrent();
		}
		return static::$shop;
	}
	
	public static function setShop( Shops_Shop $shop ): void
	{
		static::$shop = $shop;
	}
	
	
	
	public static function getCfgFilePath() : string
	{
		return SysConf_Path::getConfig().'shop/managers/shop/'.static::getShop()->getKey().'.php';
	}
	
	protected static function registerManagers() : void
	{

		static::registerManager(
			interface_class_name: Shop_Managers_Image::class,
			is_mandatory: true,
			name: 'Images',
			description: '',
			module_name_prefix: 'Shop.'
		);
		
		static::registerManager(
			interface_class_name: Shop_Managers_PriceFormatter::class,
			is_mandatory: true,
			name: 'Price formatter',
			description: '',
			module_name_prefix: 'Shop.'
		);
		
		static::registerManager(
			interface_class_name: Shop_Managers_ShoppingCart::class,
			is_mandatory: true,
			name: 'Shopping Cart',
			description: '',
			module_name_prefix: 'Shop.'
		);
		
		static::registerManager(
			interface_class_name: Shop_Managers_CashDesk::class,
			is_mandatory: true,
			name: 'Cash Desk',
			description: '',
			module_name_prefix: 'Shop.'
		);
		
		static::registerManager(
			interface_class_name: Shop_Managers_FulltextSearch::class,
			is_mandatory: true,
			name: 'Fulltext Search',
			description: '',
			module_name_prefix: 'Shop.'
		);
		
		static::registerManager(
			interface_class_name: Shop_Managers_ProductListing::class,
			is_mandatory: true,
			name: 'Product Listing',
			description: '',
			module_name_prefix: 'Shop.'
		);
		
		static::registerManager(
			interface_class_name: Shop_Managers_CustomerLogin::class,
			is_mandatory: true,
			name: 'Customer Login',
			description: '',
			module_name_prefix: 'Shop.'
		);
		
		static::registerManager(
			interface_class_name: Shop_Managers_CustomerPasswordReset::class,
			is_mandatory: true,
			name: 'Customer Password Reset',
			description: '',
			module_name_prefix: 'Shop.'
		);
		
		static::registerManager(
			interface_class_name: Shop_Managers_UI::class,
			is_mandatory: true,
			name: 'UI',
			description: '',
			module_name_prefix: 'Shop.'
		);
		
		
		static::registerManager(
			interface_class_name: Shop_Managers_AutoOffers::class,
			is_mandatory: false,
			name: 'Auto Offers',
			description: '',
			module_name_prefix: 'Shop.'
		);
		
		static::registerManager(
			interface_class_name: Shop_Managers_Compare::class,
			is_mandatory: false,
			name: 'Compare products',
			description: '',
			module_name_prefix: 'Shop.'
		);
		
		static::registerManager(
			interface_class_name: Shop_Managers_Wishlist::class,
			is_mandatory: false,
			name: 'Wishlist',
			description: '',
			module_name_prefix: 'Shop.'
		);
		
		static::registerManager(
			interface_class_name: Shop_Managers_Banners::class,
			is_mandatory: false,
			name: 'Banners',
			description: '',
			module_name_prefix: 'Shop.'
		);
		
		static::registerManager(
			interface_class_name: Shop_Managers_Catalog::class,
			is_mandatory: false,
			name: 'Catalog',
			description: '',
			module_name_prefix: 'Shop.'
		);
		
		static::registerManager(
			interface_class_name: Shop_Managers_OAuth::class,
			is_mandatory: false,
			name: 'OAuth',
			description: '',
			module_name_prefix: 'Shop.'
		);
		
		static::registerManager(
			interface_class_name: Shop_Managers_ProductReviews::class,
			is_mandatory: false,
			name: 'Product reviews',
			description: '',
			module_name_prefix: 'Shop.'
		);
		
		static::registerManager(
			interface_class_name: Shop_Managers_ProductQuestions::class,
			is_mandatory: false,
			name: 'Product questions',
			description: '',
			module_name_prefix: 'Shop.'
		);
		
		static::registerManager(
			interface_class_name: Shop_Managers_PromoAreas::class,
			is_mandatory: false,
			name: 'Promo areas',
			description: '',
			module_name_prefix: 'Shop.'
		);
		
		static::registerManager(
			interface_class_name: Shop_Managers_Articles::class,
			is_mandatory: false,
			name: 'Articles',
			description: '',
			module_name_prefix: 'Shop.'
		);
		
		static::registerManager(
			interface_class_name: Shop_Managers_MagicTags::class,
			is_mandatory: false,
			name: 'Magic tags',
			description: '',
			module_name_prefix: 'Shop.'
		);
		
		static::registerManager(
			interface_class_name: Shop_Managers_Analytics::class,
			is_mandatory: false,
			name: 'Analytics',
			description: '',
			module_name_prefix: 'Shop.'
		);
		
		
		static::registerManager(
			interface_class_name: Shop_CookieSettings_Manager::class,
			is_mandatory: false,
			name: 'Cookie Settings',
			description: '',
			module_name_prefix: 'Shop.'
		);
		
		static::registerManager(
			interface_class_name: Shop_Managers_CustomerSection::class,
			is_mandatory: false,
			name: 'Customer Section',
			description: '',
			module_name_prefix: 'Shop.'
		);
		
		
	}
	
	
	public static function Image() : Shop_Managers_Image|Application_Module
	{
		return static::get( Shop_Managers_Image::class );
	}
	
	public static function PriceFormatter() : Shop_Managers_PriceFormatter|Application_Module
	{
		return static::get( Shop_Managers_PriceFormatter::class );
	}
	
	public static function ShoppingCart() : Shop_Managers_ShoppingCart|Application_Module
	{
		return static::get( Shop_Managers_ShoppingCart::class );
	}
	
	public static function CashDesk() : Shop_Managers_CashDesk|Application_Module
	{
		return static::get( Shop_Managers_CashDesk::class );
	}
	
	public static function FulltextSearch() : Shop_Managers_FulltextSearch|Application_Module
	{
		return static::get( Shop_Managers_FulltextSearch::class );
	}
	
	public static function ProductListing() : Shop_Managers_ProductListing|Application_Module
	{
		return static::get( Shop_Managers_ProductListing::class );
	}
	
	public static function CustomerLogin() : Shop_Managers_CustomerLogin|Application_Module
	{
		return static::get( Shop_Managers_CustomerLogin::class );
	}
	
	public static function CustomerPasswordReset() : Shop_Managers_CustomerPasswordReset|Application_Module
	{
		return static::get( Shop_Managers_CustomerPasswordReset::class );
	}
	
	public static function AutoOffers() : Shop_Managers_AutoOffers|Application_Module|null
	{
		return static::get( Shop_Managers_AutoOffers::class );
	}
	
	
	public static function Compare() : Shop_Managers_Compare|Application_Module|null
	{
		return static::get( Shop_Managers_Compare::class );
	}
	
	public static function Wishlist() : Shop_Managers_Wishlist|Application_Module|null
	{
		return static::get( Shop_Managers_Wishlist::class );
	}
	
	public static function Catalog() : Shop_Managers_Catalog|Application_Module|null
	{
		return static::get( Shop_Managers_Catalog::class );
	}
	
	
	public static function OAuth() : Shop_Managers_OAuth|Application_Module|null
	{
		return static::get( Shop_Managers_OAuth::class );
	}
	
	public static function ProductReviews() : Shop_Managers_ProductReviews|Application_Module|null
	{
		return static::get( Shop_Managers_ProductReviews::class );
	}
	
	public static function ProductQuestions() : Shop_Managers_ProductQuestions|Application_Module|null
	{
		return static::get( Shop_Managers_ProductQuestions::class );
	}
	
	public static function Shop_CookieSettings() : Shop_CookieSettings_Manager|Application_Module|null
	{
		return static::get( Shop_CookieSettings_Manager::class );
	}
	
	public static function PromoAreas() : Shop_Managers_PromoAreas|Application_Module|null
	{
		return static::get( Shop_Managers_PromoAreas::class );
	}
	
	public static function Banners() : Shop_Managers_Banners|Application_Module|null
	{
		return static::get( Shop_Managers_Banners::class );
	}
	
	public static function Articles() : Shop_Managers_Articles|Application_Module|null
	{
		return static::get( Shop_Managers_Articles::class );
	}
	
	public static function MagicTags() : Shop_Managers_MagicTags|Application_Module|null
	{
		return static::get( Shop_Managers_MagicTags::class );
	}
	
	public static function Analytics() : Shop_Managers_Analytics|Application_Module|null
	{
		return static::get( Shop_Managers_Analytics::class );
	}
	
	public static function UI() : Shop_Managers_UI|Application_Module
	{
		return static::get( Shop_Managers_UI::class );
	}
	
	public static function CustomerSection() : Shop_Managers_CustomerSection|Application_Module
	{
		return static::get( Shop_Managers_CustomerSection::class );
	}


	
}
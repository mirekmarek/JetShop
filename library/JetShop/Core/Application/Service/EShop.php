<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use JetApplication\Application_Service_EShop_DiscountModule;
use JetApplication\Application_Service_EShop_AnalyticsService;
use JetApplication\Application_Service_EShop_OAuthBackendModule;
use JetApplication\Application_Service_EShop_SMSSender;
use JetApplication\EShopConfig;
use JetApplication\Application_Service_EShop_AnalyticsManager;
use JetApplication\Application_Service_EShop_Banners;
use JetApplication\Application_Service_EShop_Image;
use JetApplication\Application_Service_EShop_PriceFormatter;
use JetApplication\Application_Service_EShop_ShoppingCart;
use JetApplication\Application_Service_EShop_CashDesk;
use JetApplication\Application_Service_EShop_FulltextSearch;
use JetApplication\Application_Service_EShop_ProductListing;
use JetApplication\Application_Service_EShop_CustomerLogin;
use JetApplication\Application_Service_EShop_CustomerPasswordReset;
use JetApplication\Application_Service_EShop_AutoOffers;
use JetApplication\Application_Service_EShop_Compare;
use JetApplication\Application_Service_EShop_Wishlist;
use JetApplication\Application_Service_EShop_Catalog;
use JetApplication\Application_Service_EShop_ProductReviews;
use JetApplication\Application_Service_EShop_ProductQuestions;
use JetApplication\Application_Service_EShop_PromoAreas;
use JetApplication\Application_Service_EShop_Articles;
use JetApplication\Application_Service_EShop_UI;
use JetApplication\Application_Service_EShop_OAuth;
use JetApplication\Application_Service_EShop_MagicTags;
use JetApplication\Application_Service_EShop_CustomerSection;
use JetApplication\Application_Service_EShop_CookieSettings;
use JetApplication\Application_Service_EShop_NewOrderPostprocessor;
use JetApplication\Application_Service_EShop_EMailMarketingSubscribeManagerBackend;
use JetApplication\EShops;
use JetApplication\EShop;
use JetApplication\Application_Service_List;

class Core_Application_Service_EShop
{
	public const GROUP = 'eshop';
	
	protected static ?EShop $eshop = null;
	
	/**
	 * @var array<string,Application_Service_List>
	 */
	protected static array $list = [];
	
	public static function list( ?EShop $eshop=null ): Application_Service_List
	{
		$eshop = $eshop?:EShops::getCurrent();
		
		if(!isset( static::$list[$eshop->getKey()])) {
			static::$list[$eshop->getKey()] = new Application_Service_List(
				EShopConfig::getRootDir().'services/shop/'.$eshop->getKey().'.php',
				static::GROUP
			);
		}
		
		return static::$list[$eshop->getKey()];
	}
	
	
	/**
	 * @param EShop $eshop
	 * @return array<string,Application_Service_EShop_DiscountModule>
	 */
	public static function DiscountModules( EShop $eshop ) : array
	{
		return static::list( $eshop )->getList( Application_Service_EShop_DiscountModule::class );
	}
	
	
	public static function Image( ?EShop $eshop=null ) : Application_Service_EShop_Image|Application_Module
	{
		return static::list( $eshop )->get( Application_Service_EShop_Image::class );
	}
	
	public static function PriceFormatter( ?EShop $eshop=null ) : Application_Service_EShop_PriceFormatter|Application_Module
	{
		return static::list( $eshop )->get( Application_Service_EShop_PriceFormatter::class );
	}
	
	public static function ShoppingCart( ?EShop $eshop=null ) : Application_Service_EShop_ShoppingCart|Application_Module
	{
		return static::list( $eshop )->get( Application_Service_EShop_ShoppingCart::class );
	}
	
	public static function CashDesk( ?EShop $eshop=null ) : Application_Service_EShop_CashDesk|Application_Module
	{
		return static::list( $eshop )->get( Application_Service_EShop_CashDesk::class );
	}
	
	public static function FulltextSearch( ?EShop $eshop=null ) : Application_Service_EShop_FulltextSearch|Application_Module
	{
		return static::list( $eshop )->get( Application_Service_EShop_FulltextSearch::class );
	}
	
	public static function ProductListing( ?EShop $eshop=null ) : Application_Service_EShop_ProductListing|Application_Module
	{
		return static::list( $eshop )->get( Application_Service_EShop_ProductListing::class );
	}
	
	public static function CustomerLogin( ?EShop $eshop=null ) : Application_Service_EShop_CustomerLogin|Application_Module
	{
		return static::list( $eshop )->get( Application_Service_EShop_CustomerLogin::class );
	}
	
	public static function CustomerPasswordReset( ?EShop $eshop=null ) : Application_Service_EShop_CustomerPasswordReset|Application_Module
	{
		return static::list( $eshop )->get( Application_Service_EShop_CustomerPasswordReset::class );
	}
	
	public static function AutoOffers( ?EShop $eshop=null ) : Application_Service_EShop_AutoOffers|Application_Module|null
	{
		return static::list( $eshop )->get( Application_Service_EShop_AutoOffers::class );
	}
	
	public static function Compare( ?EShop $eshop=null ) : Application_Service_EShop_Compare|Application_Module|null
	{
		return static::list( $eshop )->get( Application_Service_EShop_Compare::class );
	}
	
	public static function Wishlist( ?EShop $eshop=null ) : Application_Service_EShop_Wishlist|Application_Module|null
	{
		return static::list( $eshop )->get( Application_Service_EShop_Wishlist::class );
	}
	
	public static function Catalog( ?EShop $eshop=null ) : Application_Service_EShop_Catalog|Application_Module|null
	{
		return static::list( $eshop )->get( Application_Service_EShop_Catalog::class );
	}
	
	public static function OAuthManager( ?EShop $eshop=null ) : Application_Service_EShop_OAuth|Application_Module|null
	{
		return static::list( $eshop )->get( Application_Service_EShop_OAuth::class );
	}
	
	/**
	 * @return array<string,Application_Service_EShop_OAuthBackendModule>
	 */
	public static function OAuthModules( ?EShop $eshop=null ) : array
	{
		return static::list( $eshop )->getList( Application_Service_EShop_OAuthBackendModule::class );
	}
	
	public static function ProductReviews( ?EShop $eshop=null ) : Application_Service_EShop_ProductReviews|Application_Module|null
	{
		return static::list( $eshop )->get( Application_Service_EShop_ProductReviews::class );
	}
	
	public static function ProductQuestions( ?EShop $eshop=null ) : Application_Service_EShop_ProductQuestions|Application_Module|null
	{
		return static::list( $eshop )->get( Application_Service_EShop_ProductQuestions::class );
	}
	
	public static function CookieSettings( ?EShop $eshop=null ) : Application_Service_EShop_CookieSettings|Application_Module|null
	{
		return static::list( $eshop )->get( Application_Service_EShop_CookieSettings::class );
	}
	
	public static function PromoAreas( ?EShop $eshop=null ) : Application_Service_EShop_PromoAreas|Application_Module|null
	{
		return static::list( $eshop )->get( Application_Service_EShop_PromoAreas::class );
	}
	
	public static function Banners( ?EShop $eshop=null ) : Application_Service_EShop_Banners|Application_Module|null
	{
		return static::list( $eshop )->get( Application_Service_EShop_Banners::class );
	}
	
	public static function Articles( ?EShop $eshop=null ) : Application_Service_EShop_Articles|Application_Module|null
	{
		return static::list( $eshop )->get( Application_Service_EShop_Articles::class );
	}
	
	public static function MagicTags( ?EShop $eshop=null ) : Application_Service_EShop_MagicTags|Application_Module|null
	{
		return static::list( $eshop )->get( Application_Service_EShop_MagicTags::class );
	}
	
	public static function AnalyticsManager( ?EShop $eshop=null ) : Application_Service_EShop_AnalyticsManager|Application_Module|null
	{
		return static::list( $eshop )->get( Application_Service_EShop_AnalyticsManager::class );
	}
	
	/**
	 * @return array<string,Application_Service_EShop_AnalyticsService>
	 */
	public static function AnalyticsServices( ?EShop $eshop=null ) : array
	{
		return static::list( $eshop )->getList( Application_Service_EShop_AnalyticsService::class );
	}
	
	
	public static function UI( ?EShop $eshop=null ) : Application_Service_EShop_UI|Application_Module
	{
		return static::list( $eshop )->get( Application_Service_EShop_UI::class );
	}
	
	public static function CustomerSection( ?EShop $eshop=null ) : Application_Service_EShop_CustomerSection|Application_Module
	{
		return static::list( $eshop )->get( Application_Service_EShop_CustomerSection::class );
	}
	
	/**
	 * @param EShop $eshop
	 * @return array<string,Application_Service_EShop_NewOrderPostprocessor>
	 */
	public static function NewOrderPostprocessors( EShop $eshop ) : array
	{
		return static::list( $eshop )->getList( Application_Service_EShop_NewOrderPostprocessor::class );
	}
	
	public static function EMailMarketingSubscribeManagerBackend( ?EShop $eshop=null ) : Application_Service_EShop_EMailMarketingSubscribeManagerBackend|Application_Module|null
	{
		return static::list( $eshop )->get( Application_Service_EShop_EMailMarketingSubscribeManagerBackend::class );
	}
	
	public static function SMSSender( ?EShop $eshop=null ) : Application_Service_EShop_SMSSender|Application_Module|null
	{
		return static::list( $eshop )->get( Application_Service_EShop_SMSSender::class );
	}
	
}
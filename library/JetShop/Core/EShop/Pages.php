<?php

namespace JetShop;

use Jet\MVC;
use Jet\MVC_Page_Interface;
use JetApplication\EShop_PageDefinition;
use JetApplication\EShopConfig;
use JetApplication\EShops;
use JetApplication\EShop;



class Core_EShop_Pages
{
	protected static ?array $pages_template =  null;
	
	protected static function loadPages(): void
	{
		if(static::$pages_template===null) {
			static::$pages_template = require EShopConfig::getRootDir().'default_pages_template.php';
		}
	}
	
	public static function registerPage( string $key, EShop_PageDefinition $definition ) : void
	{
		static::loadPages();
		static::$pages_template[$key] = $definition->toArray();
	}
	
	/**
	 * @return EShop_PageDefinition[]
	 */
	public static function getDefinitions() : array
	{
		static::loadPages();
		
		$definitions = [];
		foreach( static::$pages_template as $key=> $d ) {
			$definitions[$key] = EShop_PageDefinition::fromArray( $d );
			$definitions[$key]->setKey( $key );
		}
		
		return $definitions;
	}
	
	public static function getPageDefinition( string $key ) : ?EShop_PageDefinition
	{
		static::loadPages();
		
		if( !isset( static::$pages_template[$key] ) ) {
			return null;
		}
		
		return EShop_PageDefinition::fromArray( static::$pages_template[$key] );
	}
	
	public static function getPage( string $key, ?EShop $eshop=null ) : ?MVC_Page_Interface
	{
		static::loadPages();
		
		if( !$eshop ) {
			$eshop = EShops::getCurrent();
		}
		
		return MVC::getPage( static::$pages_template[$key]['id'], $eshop->getLocale(), $eshop->getBaseId() );
	}
	
	
	public static function createPages( EShop $eshop ) : void
	{
		foreach(static::getDefinitions() as $definition) {
			$page = $definition->createPageDefinition( $eshop );
			
			$page->saveDataFile();
		}
	}
	
	
	
	
	
	
	public static function ChangePassword( ?EShop $eshop = null ): ?MVC_Page_Interface
	{
		return static::getPage( 'change_password', $eshop );
	}
	
	public static function ResetPassword( ?EShop $eshop = null ): ?MVC_Page_Interface
	{
		return static::getPage( 'password_reset', $eshop );
	}
	
	public static function SignUp( ?EShop $eshop = null ): ?MVC_Page_Interface
	{
		return static::getPage( 'sign_up', $eshop );
	}
	
	public static function Login( ?EShop $eshop = null ): ?MVC_Page_Interface
	{
		return static::getPage( 'login', $eshop );
	}
	
	public static function CustomerSection( ?EShop $eshop = null ): MVC_Page_Interface
	{
		return static::getPage( 'customer_section', $eshop );
	}
	
	
	public static function CashDesk( ?EShop $eshop = null ): MVC_Page_Interface
	{
		return static::getPage( 'cash_desk', $eshop );
	}
	
	public static function CashDeskConfirmation( ?EShop $eshop = null ): MVC_Page_Interface
	{
		return static::getPage( 'cash_desk_confirmation', $eshop );
	}
	
	public static function CashDeskPayment( ?EShop $eshop = null ): MVC_Page_Interface
	{
		return static::getPage( 'cash_desk_payment', $eshop );
	}
	
	public static function ShoppingCart( ?EShop $eshop = null ): MVC_Page_Interface
	{
		return static::getPage( 'shopping_cart', $eshop );
	}
	
	public static function Search( ?EShop $eshop = null ): MVC_Page_Interface
	{
		return static::getPage( 'search', $eshop );
	}
	
	public static function SearchWhisperer( ?EShop $eshop = null ): MVC_Page_Interface
	{
		return static::getPage( 'search_whisper', $eshop );
	}

	
	public static function Compare( ?EShop $eshop = null ): ?MVC_Page_Interface
	{
		return static::getPage( 'compare', $eshop );
	}
	
	public static function Wishlist( ?EShop $eshop = null ): ?MVC_Page_Interface
	{
		return static::getPage( 'wishlist', $eshop );
	}
	
	
	public static function Complaints( ?EShop $eshop = null ): ?MVC_Page_Interface
	{
		return static::getPage( 'complaints', $eshop );
	}
	
	public static function ReturnOfGoods( ?EShop $eshop = null ): ?MVC_Page_Interface
	{
		return static::getPage( 'return_of_goods', $eshop );
	}
	
	public static function OAuth( ?EShop $eshop = null ): ?MVC_Page_Interface
	{
		return static::getPage( 'oauth', $eshop );
	}
	
	
}
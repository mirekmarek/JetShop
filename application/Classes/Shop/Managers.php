<?php
namespace JetApplication;

use Jet\Application_Module;
use Jet\Application_Modules;
use Jet\Exception;

class Shop_Managers {
	
	protected static array $managers = [];
	
	public static function Image() : Shop_Managers_Image|Application_Module
	{
		return static::findManager( Shop_Managers_Image::class );
	}
	
	public static function PriceFormatter() : Shop_Managers_PriceFormatter|Application_Module
	{
		return static::findManager( Shop_Managers_PriceFormatter::class );
	}
	
	public static function ShoppingCart() : Shop_Managers_ShoppingCart|Application_Module
	{
		return static::findManager( Shop_Managers_ShoppingCart::class );
	}
	
	public static function CashDesk() : Shop_Managers_CashDesk|Application_Module
	{
		return static::findManager( Shop_Managers_CashDesk::class );
	}
	
	
	public static function findManager( string $manager_interface, bool $service_is_mandatory=false ) : mixed
	{
		if( isset(static::$managers[$manager_interface]) ) {
			return static::$managers[$manager_interface];
		}
		
		$modules = Application_Modules::activatedModulesList();
		foreach($modules as $manifest) {
			if(!str_starts_with($manifest->getName(), 'Shop.') ) {
				continue;
			}
			
			$module = Application_Modules::moduleInstance( $manifest->getName() );
			
			if($module instanceof $manager_interface) {
				static::$managers[$manager_interface] = $module;
				
				return $module;
			}
		}
		
		if($service_is_mandatory) {
			throw new Exception('Mandatory service '.$manager_interface.' is not available');
		}
		
		return null;
	}
	
}
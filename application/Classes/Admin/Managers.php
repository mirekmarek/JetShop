<?php
namespace JetApplication;

use Jet\Application_Module;
use Jet\Application_Modules;
use Jet\Exception;

class Admin_Managers {
	
	protected static array $managers = [];
	
	public static function Category() : Admin_Managers_Category|Application_Module
	{
		return static::findManager( Admin_Managers_Category::class );
	}
	
	public static function Product() : Admin_Managers_Product|Application_Module
	{
		return static::findManager( Admin_Managers_Product::class );
	}
	
	
	public static function Property() : Admin_Managers_Property|Application_Module
	{
		return static::findManager( Admin_Managers_Property::class );
	}
	
	public static function PropertyGroup() : Admin_Managers_PropertyGroup|Application_Module
	{
		return static::findManager( Admin_Managers_PropertyGroup::class );
	}
	
	public static function KindOfProduct() : Admin_Managers_KindOfProduct|Application_Module
	{
		return static::findManager( Admin_Managers_KindOfProduct::class );
	}
	
	
	public static function Image() : Admin_Managers_Image|Application_Module
	{
		return static::findManager( Admin_Managers_Image::class );
	}
	
	public static function UI() : Admin_Managers_UI|Application_Module
	{
		return static::findManager( Admin_Managers_UI::class );
	}
	
	public static function FulltextSearch() : Admin_Managers_FulltextSearch|Application_Module
	{
		return static::findManager( Admin_Managers_FulltextSearch::class );
	}
	
	public static function EntityListing() : Admin_Managers_Entity_Listing|Application_Module
	{
		return static::findManager( Admin_Managers_Entity_Listing::class );
	}
	
	public static function EntityEdit_WithShopData() : Admin_Managers_Entity_Edit_WithShopData|Application_Module
	{
		return static::findManager( Admin_Managers_Entity_Edit_WithShopData::class );
	}
	
	public static function EntityEdit_Common() : Admin_Managers_Entity_Edit_Common|Application_Module
	{
		return static::findManager( Admin_Managers_Entity_Edit_Common::class );
	}
	
	public static function ProductFilter() : Admin_Managers_ProductFilter|Application_Module
	{
		return static::findManager( Admin_Managers_ProductFilter::class );
	}
	
	public static function PriceFormatter() : Admin_Managers_PriceFormatter|Application_Module
	{
		return static::findManager( Admin_Managers_PriceFormatter::class );
	}
	
	
	public static function Order() : Admin_Managers_Order|Application_Module
	{
		return static::findManager( Admin_Managers_Order::class );
	}
	
	public static function Customer() : Admin_Managers_Customer|Application_Module
	{
		return static::findManager( Admin_Managers_Customer::class );
	}
	
	public static function findManager( string $manager_interface, bool $service_is_mandatory=false ) : mixed
	{
		if( isset(static::$managers[$manager_interface]) ) {
			return static::$managers[$manager_interface];
		}
		
		$modules = Application_Modules::activatedModulesList();
		foreach($modules as $manifest) {
			if(!str_starts_with($manifest->getName(), 'Admin.') ) {
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
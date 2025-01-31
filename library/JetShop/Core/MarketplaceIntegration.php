<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;



use JetApplication\MarketplaceIntegration_Module;
use JetApplication\Managers;
use JetApplication\Order;
use JetApplication\Order_Event;
use JetApplication\EShops;

abstract class Core_MarketplaceIntegration
{
	
	protected static string $module_name_prefix = 'MarketplaceIntegration.';
	
	protected static ?string $root_path = null;
	
	public static function getModuleNamePrefix(): string
	{
		return self::$module_name_prefix;
	}
	
	public static function setModuleNamePrefix( string $module_name_prefix ): void
	{
		self::$module_name_prefix = $module_name_prefix;
	}
	
	
	
	/**
	 * @return MarketplaceIntegration_Module[]
	 */
	public static function getActiveModules() : iterable
	{
		$modules = [];

		foreach( Managers::findManagers( MarketplaceIntegration_Module::class, static::getModuleNamePrefix() ) as $module) {
			/**
			 * @var MarketplaceIntegration_Module $module
			 */
			$modules[$module->getCode()] = $module;
		}
		
		return $modules;
	}
	
	public static function getActiveModule( string $module ) : ?MarketplaceIntegration_Module
	{
		$modules = static::getActiveModules();

		if(!isset($modules[$module])) {
			return null;
		}
		
		return $modules[$module];
	}
	
	public static function getScope() : array
	{
		$res = [];
		
		foreach( static::getActiveModules() as $mp ) {
			foreach( EShops::getList() as $eshop ) {
				if( $mp->isAllowedForShop( $eshop ) ) {
					if(EShops::isMultiEShopMode()) {
						$res[$mp->getCode().':'.$eshop->getKey()] = $mp->getTitle().' - '.$eshop->getName();
					} else {
						$res[$mp->getCode().':'.$eshop->getKey()] = $mp->getTitle();
					}
				}
			}
		}
		
		return $res;
	}
	
	public static function getOrderHandler( Order $order ) : ?MarketplaceIntegration_Module
	{
		foreach(static::getActiveModules() as $mp) {
			if( $mp->orderIsRelevant( $order ) ) {
				return $mp;
			}
		}
		
		return null;
	}
	
	public static function handleOrderEvent( Order_Event $order_event ) : bool|null
	{
		$order = $order_event->getOrder();
		
		foreach(static::getActiveModules() as $mp) {
			if( $mp->orderIsRelevant( $order ) ) {
				return $mp->handleOrderEvent( $order_event );
			}
		}
		
		return null;
	}
	
	public static function getSourcesScope() : array
	{
		$res = [];
		foreach(static::getActiveModules() as $mp) {
			$res[$mp->getImportSource()] = $mp->getImportSource();
		}
		
		return $res;
	}
}
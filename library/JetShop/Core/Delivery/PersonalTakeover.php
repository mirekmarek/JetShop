<?php
/**
 *
 */

namespace JetShop;


use Jet\Application_Modules;

use JetApplication\Delivery_Method_ShopData;
use JetApplication\Shops_Shop;
use JetApplication\Delivery_Method_Module_PersonalTakeover;
use JetApplication\Delivery_Method;


abstract class Core_Delivery_PersonalTakeover {


	public static function actualizePlaces( Shops_Shop $shop, bool $verbose=false ) : void
	{

		$methods = Delivery_Method_ShopData::fetchInstances(
			$shop->getWhere()
		);
		
		$updated = false;
		$future_list = [];
		
		foreach( $methods as $method ) {
			
			if(!$method->isPersonalTakeover()) {
				continue;
			}
			
			$module = $method->getBackendModule();
			if(!$module) {
				continue;
			}
			
			$module->actualizePlaces( $method, $verbose );
			
		}
	}

	/**
	 * @return Delivery_Method_Module_PersonalTakeover[]
	 */
	public static function getActiveModules() : iterable
	{
		$methods = Delivery_Method::fetchInstances();

		$modules = [];
		foreach($methods as $method) {
			if(!$method->isPersonalTakeover()) {
				continue;
			}

			$module_name = $method->getBackendModuleName();

			if(!Application_Modules::moduleIsActivated($module_name)) {
				continue;
			}

			$module = $method->getBackendModule();
			$manifest = $module->getModuleManifest();

			$modules[$manifest->getName()] = $module;
		}

		return $modules;
	}
}
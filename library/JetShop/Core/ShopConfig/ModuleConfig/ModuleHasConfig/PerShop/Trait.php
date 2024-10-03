<?php
namespace JetShop;

use Jet\Application_Module;
use JetApplication\ShopConfig_ModuleConfig_PerShop;
use JetApplication\Shops_Shop;

trait Core_ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait
{
	/**
	 * @var ShopConfig_ModuleConfig_PerShop[]
	 */
	protected array $shop_configs = [];
	
	public function getShopConfig( Shops_Shop $shop ) : ShopConfig_ModuleConfig_PerShop
	{
		/**
		 * @var Application_Module $this
		 */
		if(!isset($this->shop_configs[$shop->getKey()])) {
			$class_name = $this->module_manifest->getNamespace().'Config_PerShop';
			
			$this->shop_configs[$shop->getKey()] = new $class_name( $this->module_manifest, $shop );
		}
		
		return $this->shop_configs[$shop->getKey()];
	}
}
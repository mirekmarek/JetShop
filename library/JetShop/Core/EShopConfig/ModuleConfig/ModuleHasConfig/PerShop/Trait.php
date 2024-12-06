<?php
namespace JetShop;

use Jet\Application_Module;
use JetApplication\EShopConfig_ModuleConfig_PerShop;
use JetApplication\EShop;

trait Core_EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait
{
	/**
	 * @var EShopConfig_ModuleConfig_PerShop[]
	 */
	protected array $eshop_configs = [];
	
	public function getEshopConfig( EShop $eshop ) : EShopConfig_ModuleConfig_PerShop
	{
		/**
		 * @var Application_Module $this
		 */
		if(!isset( $this->eshop_configs[$eshop->getKey()])) {
			$class_name = $this->module_manifest->getNamespace().'Config_PerShop';
			
			$this->eshop_configs[$eshop->getKey()] = new $class_name( $this->module_manifest, $eshop );
		}
		
		return $this->eshop_configs[$eshop->getKey()];
	}
}
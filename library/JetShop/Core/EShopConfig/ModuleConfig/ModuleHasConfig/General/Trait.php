<?php
namespace JetShop;

use Jet\Application_Module;
use JetApplication\EShopConfig_ModuleConfig_General;

trait Core_EShopConfig_ModuleConfig_ModuleHasConfig_General_Trait
{
	/**
	 * @var EShopConfig_ModuleConfig_General|null
	 */
	protected ?EShopConfig_ModuleConfig_General $general_config = null;
	
	public function getGeneralConfig() : EShopConfig_ModuleConfig_General
	{
		/**
		 * @var Application_Module $this
		 */
		if(!$this->general_config ) {
			$class_name = $this->module_manifest->getNamespace().'Config_General';
			$this->general_config = new $class_name( $this->module_manifest );
			
		}
		
		return $this->general_config;
	}
}
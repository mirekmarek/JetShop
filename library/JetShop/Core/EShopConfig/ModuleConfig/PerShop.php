<?php
namespace JetShop;

use Jet\Application_Module_Manifest;
use Jet\IO_File;
use JetApplication\EShopConfig;
use JetApplication\EShopConfig_ModuleConfig_General;
use JetApplication\EShop;

abstract class Core_EShopConfig_ModuleConfig_PerShop extends EShopConfig_ModuleConfig_General {
	
	protected EShop $eshop;
	
	public function __construct( Application_Module_Manifest $module, EShop $eshop, ?array $data = null )
	{
		$this->module = $module;
		$this->eshop = $eshop;
		
		if($data===null) {
			$this->_config_file_path = EShopConfig::getRootDir().$module->getName().'/' . $eshop->getKey() . '.php';
			
			if(!IO_File::exists($this->_config_file_path)) {
				$this->initNewConfigFile();
			}
		}
		
		if( $data === null ) {
			$data = $this->readConfigFileData();
		}
		
		$this->setData( $data );
		
	}
	
	public function getEshop(): EShop
	{
		return $this->eshop;
	}
	
	
	
	protected function initNewConfigFile() : void
	{
		$this->saveConfigFile();
	}
}
<?php
namespace JetShop;

use Jet\Application_Module_Manifest;
use Jet\IO_File;
use Jet\SysConf_Path;
use JetApplication\ShopConfig_ModuleConfig_General;
use JetApplication\Shops_Shop;

abstract class Core_ShopConfig_ModuleConfig_PerShop extends ShopConfig_ModuleConfig_General {
	
	protected Shops_Shop $shop;
	
	public function __construct( Application_Module_Manifest $module, Shops_Shop $shop, ?array $data = null )
	{
		$this->module = $module;
		$this->shop = $shop;
		
		if($data===null) {
			$this->_config_file_path = SysConf_Path::getConfig() . 'shop/'.$module->getName().'/' . $shop->getKey() . '.php';
			
			if(!IO_File::exists($this->_config_file_path)) {
				$this->initNewConfigFile();
			}
		}
		
		if( $data === null ) {
			$data = $this->readConfigFileData();
		}
		
		$this->setData( $data );
		
	}
	
	public function getShop(): Shops_Shop
	{
		return $this->shop;
	}
	
	
	
	protected function initNewConfigFile() : void
	{
		$this->saveConfigFile();
	}
}
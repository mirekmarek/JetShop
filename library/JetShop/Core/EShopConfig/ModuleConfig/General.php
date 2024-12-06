<?php
namespace JetShop;

use Jet\Application_Module_Manifest;
use Jet\Config;
use Jet\IO_File;
use JetApplication\EShopConfig;

abstract class Core_EShopConfig_ModuleConfig_General extends Config {
	
	protected Application_Module_Manifest $module;
	
	public function __construct( Application_Module_Manifest $module, ?array $data = null )
	{
		$this->module = $module;
		
		if($data===null) {
			$this->_config_file_path = EShopConfig::getRootDir().$module->getName() . '/general.php';
			
			if(!IO_File::exists($this->_config_file_path)) {
				$this->saveConfigFile();
			}
		}
		
		if( $data === null ) {
			$data = $this->readConfigFileData();
		}
		
		$this->setData( $data );

	}
}
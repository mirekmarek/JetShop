<?php
namespace JetShop;

use Jet\Application_Module_Manifest;
use Jet\Config;
use Jet\IO_File;
use Jet\SysConf_Path;

abstract class Core_ShopConfig_ModuleConfig_General extends Config {
	
	protected Application_Module_Manifest $module;
	
	public function __construct( Application_Module_Manifest $module, ?array $data = null )
	{
		$this->module = $module;
		
		if($data===null) {
			$this->_config_file_path = SysConf_Path::getConfig() . 'shop/'.$module->getName() . '/general.php';
			
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
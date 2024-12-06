<?php
namespace JetShop;

use Jet\SysConf_Path;

abstract class Core_EShopConfig {
	
	public static function getRootDir() : string
	{
		return SysConf_Path::getConfig() . 'eshop/';
	}
}
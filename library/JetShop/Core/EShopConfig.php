<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\SysConf_Path;

abstract class Core_EShopConfig {
	
	public static function getRootDir() : string
	{
		return SysConf_Path::getConfig() . 'eshop/';
	}
}
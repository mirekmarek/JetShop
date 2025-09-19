<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Service_List;
use Jet\Tr;
use JetApplication\Application_Service_General;
use JetApplication\SysServices_Definition;
use JetApplication\Application_Service_General_SysServices;
use JetApplication\SysServices_Provider_Interface;

abstract class Core_SysServices {
	
	
	public static function getManager() : ?Application_Service_General_SysServices
	{
		return Application_Service_General::SysServices();
	}
	
	/**
	 * @return SysServices_Definition[]
	 */
	public static function getServiceList() : array
	{
		$list = [];
		foreach( Application_Service_List::findPossibleModules(SysServices_Provider_Interface::class) as $module) {
			Tr::setCurrentDictionaryTemporary(
				dictionary: $module->getModuleManifest()->getName(),
				action: function() use (&$list, $module) {
					foreach( $module->getSysServicesDefinitions() as $sys_service ) {
						$list[ $sys_service->getCode() ] = $sys_service;
					}
				}
			);
			
		}
		
		return $list;
	}
	
	public static function getService( string $code ) : ?SysServices_Definition
	{
		$list = static::getServiceList();
		
		return $list[$code]??null;
	}
	
}
<?php
namespace JetShop;

use Jet\Tr;
use JetApplication\Managers;
use JetApplication\Managers_General;
use JetApplication\SysServices_Definition;
use JetApplication\SysServices_Manager;
use JetApplication\SysServices_Provider_Interface;

abstract class Core_SysServices {
	
	
	public static function getManager() : ?SysServices_Manager
	{
		return Managers_General::SysServices();
	}
	
	/**
	 * @return SysServices_Definition[]
	 */
	public static function getServiceList() : array
	{
		$list = [];
		foreach( Managers::findManagers(SysServices_Provider_Interface::class) as $module) {
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
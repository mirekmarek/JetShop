<?php
namespace JetShop;

use JetApplication\Manager_MetaInfo;
use Jet\Application_Module;
use Jet\Application_Modules;
use Jet\Exception;
use Jet\IO_File;

abstract class Core_Managers {
	
	protected static ?array $map = null;
	/**
	 * @var Manager_MetaInfo[]|null
	 */
	protected static ?array $managers_meta_info = null;
	
	protected static ?array $config = null;
	
	protected static array $managers = [];
	
	abstract public static function getCfgFilePath() : string;
	
	abstract protected static function registerManagers() : void;
	
	
	public static function loadCfg() : void
	{
		if(static::$config===null) {
			$path = static::getCfgFilePath();
			
			if(!IO_File::exists($path)) {
				static::$config = [];
				static::saveCfg();
			}
			
			static::$config = require $path;
		}
	}
	
	public static function saveCfg() : void
	{
		static::loadCfg();
		
		IO_File::writeDataAsPhp(
			static::getCfgFilePath(),
			static::$config
		);
	}
	
	public static function getConfig() : array
	{
		static::loadCfg();
		
		return static::$config;
	}
	
	public static function setManagerConfig( string $interface_class_name, string $module_class_name ) : void
	{
		static::loadCfg();
		
		static::$config[$interface_class_name] = $module_class_name;
	}
	
	/**
	 * @return Manager_MetaInfo[]
	 */
	public static function getRegisteredManagers() : array
	{
		if(static::$managers_meta_info===null) {
			static::registerManagers();
		}
		
		
		return static::$managers_meta_info;
	}
	
	public static function getManagerMetaInfo( string $interface_class_name ) : ?Manager_MetaInfo
	{
		if(static::$managers_meta_info===null) {
			static::registerManagers();
		}
		
		if(!isset(static::$managers_meta_info[ $interface_class_name ])) {
			throw new Exception('Manager '.$interface_class_name.' is not registered');
		}
		
		return static::$managers_meta_info[ $interface_class_name ];
	}
	
	protected static function _registerManager( Manager_MetaInfo $manager ) : void
	{
		static::$managers_meta_info[$manager->getInterfaceClassName()] = $manager;
	
	}
	
	protected static function _registerManagers( string $group ) : void
	{
		$defimnitions = Manager_MetaInfo::getManagers( $group );
		
		
		static::$managers_meta_info = [];
		
		foreach($defimnitions as $manager) {
			static::_registerManager( $manager );
		}
		
		uasort( static::$managers_meta_info, function( Manager_MetaInfo $a, Manager_MetaInfo $b ) {
			return strcmp( $a->getName(), $b->getName() );
		} );
	}

	
	/**
	 * @return Manager_MetaInfo[]
	 */
	public static function getManagersMetaInfo() : array
	{
		if(static::$managers_meta_info===null) {
			static::$managers_meta_info=[];
			static::registerManagers();
		}
		
		return static::$managers_meta_info;
	}
	
	
	protected static function initMap() : void
	{

		if(static::$map!==null) {
			return;
		}
		
		static::$map = [];
		
		$modules = Application_Modules::activatedModulesList();
		
		foreach( $modules as $module_name => $module_info ) {
			$module = Application_Modules::moduleInstance( $module_name );
			
			$implements = class_implements( $module , false);
			
			foreach($implements as $ifc) {
				if(!isset(static::$map[$ifc])) {
					static::$map[$ifc] = [];
				}
				
				static::$map[$ifc][$module_name] = $module;
			}
			
			$parents = class_parents( $module, false );
			foreach($parents as $parent) {
				if(!isset(static::$map[$parent])) {
					static::$map[$parent] = [];
				}
				
				static::$map[$parent][$module_name] = $module;
			}
			
		}

	}
	
	/**
	 * @param string $manager_interface
	 * @param string|null $name_prefix
	 *
	 * @return Application_Module[]
	 */
	public static function findManagers( string $manager_interface, ?string $name_prefix=null ) : array
	{
		static::initMap();
		if(!isset(static::$map[$manager_interface])) {
			return [];
		}
		
		if(!$name_prefix) {
			return static::$map[$manager_interface];
		}
		
		$managers = [];
		
		foreach( static::$map[$manager_interface] as $name=>$module ) {
			if(str_starts_with($name, $name_prefix)) {
				$managers[$name] = $module;
			}
		}
		
		return $managers;
	}
	
	
	public static function get( string $manager_interface ) : ?Application_Module
	{
		if(isset(static::$managers[$manager_interface])) {
			return static::$managers[$manager_interface];
		}
		
		static::loadCfg();
		if(!array_key_exists($manager_interface, static::$config)) {

			$meta_info = static::getManagerMetaInfo( $manager_interface );
			
			foreach(static::findManagers( $manager_interface, $meta_info->getModuleNamePrefix() ) as $manager) {
				
				static::$config[$manager_interface] = $manager->getModuleManifest()->getName();
				static::$managers[$manager_interface] = $manager;
				
				static::saveCfg();
				
				return static::$managers[$manager_interface];
			}
		}
		
		if(array_key_exists($manager_interface, static::$config)) {
			$module_name = static::$config[$manager_interface];
			if(Application_Modules::moduleIsActivated($module_name)) {
				
				$manager = Application_Modules::moduleInstance( $module_name );
				static::$managers[$manager_interface] = $manager;
				
				return $manager;
			}
		}
		
		$meta_info = static::getManagerMetaInfo($manager_interface);
		
		
		if($meta_info->isMandatory()) {
			throw new Exception('Mandatory manager '.$manager_interface.' is not available');
		}
		
		return null;
	}
	
}
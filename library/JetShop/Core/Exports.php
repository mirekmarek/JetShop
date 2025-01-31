<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\SysConf_Path;

use JetApplication\Exports_Definition;
use JetApplication\Exports_Manager;
use JetApplication\Exports_Module;
use JetApplication\Managers;
use JetApplication\Managers_General;

abstract class Core_Exports
{

	protected static string $module_name_prefix = 'Exports.';

	protected static ?string $root_path = null;
	
	
	public static function getManager() : ?Exports_Manager
	{
		return Managers_General::Exports();
	}
	

	public static function getModuleNamePrefix(): string
	{
		return self::$module_name_prefix;
	}

	public static function setModuleNamePrefix( string $module_name_prefix ): void
	{
		self::$module_name_prefix = $module_name_prefix;
	}

	/**
	 * @return string|null
	 */
	public static function getRootPath(): ?string
	{
		if(!static::$root_path) {
			static::$root_path = SysConf_Path::getBase().'exports/';
		}

		return static::$root_path;
	}

	/**
	 * @param string|null $root_path
	 */
	public static function setRootPath( ?string $root_path ): void
	{
		static::$root_path = $root_path;
	}



	/**
	 * @return Exports_Module[]
	 */
	public static function getExportModulesList() : iterable
	{
		$modules = [];

		foreach( Managers::findManagers(Exports_Module::class, static::getModuleNamePrefix()) as $module) {
			/**
			 * @var Exports_Module $module
			 */

			$modules[$module->getCode()] = $module;
		}

		return $modules;
	}

	public static function getExportModule( string $code ) : ?Exports_Module
	{
		$modules = static::getExportModulesList();
		if(!isset( $modules[$code])) {
			return null;
		}

		return $modules[$code];
	}
	
	/**
	 * @return Exports_Definition[]
	 */
	public static function getExportsList() : array
	{
		$list = [];
		
		foreach(static::getExportModulesList() as $module) {
			foreach($module->getExportsDefinitions() as $export) {
				$list[$export->getCode()] = $export;
			}
		}

		return $list;
	}
	
	public static function getExport( string $code ) : ?Exports_Definition
	{
		$list = static::getExportsList();
		
		return $list[$code]??null;
	}
}
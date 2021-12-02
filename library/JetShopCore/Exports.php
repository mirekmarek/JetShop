<?php
namespace JetShop;


use Jet\Application_Modules;
use Jet\SysConf_Path;

abstract class Core_Exports
{

	protected static string $module_name_prefix = 'Exports.';

	protected static ?string $root_path = null;

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
	public static function getActiveModules() : iterable
	{
		$modules = [];

		$name_prefix = static::getModuleNamePrefix();

		foreach(Application_Modules::activatedModulesList() as $manifest) {
			if( str_starts_with( $manifest->getName(), $name_prefix ) ) {
				/**
				 * @var Exports_Module $module
				 */
				$module = Application_Modules::moduleInstance( $manifest->getName() );
				$modules[$module->getCode()] = $module;
			}
		}

		return $modules;
	}

	public static function getActiveModule( string $module ) : ?Exports_Module
	{
		$modules = static::getActiveModules();
		if(!isset($modules[$module])) {
			return null;
		}

		return $modules[$module];
	}
}
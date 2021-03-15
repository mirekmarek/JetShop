<?php
namespace JetShop;


use Jet\Application_Modules;

abstract class Core_Exports
{

	protected static string $module_name_prefix = 'Exports.';

	public static function getModuleNamePrefix(): string
	{
		return self::$module_name_prefix;
	}

	public static function setModuleNamePrefix( string $module_name_prefix ): void
	{
		self::$module_name_prefix = $module_name_prefix;
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
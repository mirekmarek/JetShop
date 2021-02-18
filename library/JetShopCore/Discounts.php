<?php
namespace JetShop;

use Jet\Application_Modules;

class Core_Discounts {

	protected static string $module_name_prefix = 'Order.Discounts.';

	public static function getModuleNamePrefix(): string
	{
		return self::$module_name_prefix;
	}

	public static function setModuleNamePrefix( string $module_name_prefix ): void
	{
		self::$module_name_prefix = $module_name_prefix;
	}


	/**
	 * @return Discounts_Module[]
	 */
	public static function getActiveModules() : iterable
	{
		$modules = [];

		$name_prefix = static::getModuleNamePrefix();

		foreach(Application_Modules::activatedModulesList() as $manifest) {
			if( str_starts_with( $manifest->getName(), $name_prefix ) ) {
				$modules[$manifest->getName()] = Application_Modules::moduleInstance( $manifest->getName() );
			}
		}

		return $modules;
	}

}
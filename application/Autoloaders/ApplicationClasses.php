<?php
/**
 *
 * @copyright Copyright (c) 2011-2021 Miroslav Marek <mirek.marek@web-jet.cz>
 * @license http://www.php-jet.net/license/license.txt
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */

namespace JetShop;

use Jet\Autoloader_Loader;
use Jet\SysConf_Path;

/**
 *
 */
class Autoloader_ApplicationClasses extends Autoloader_Loader
{

	/**
	 *
	 * @param string $root_namespace
	 * @param string $namespace
	 * @param string $class_name
	 *
	 * @return bool|string
	 */
	public function getScriptPath( string $root_namespace, string $namespace, string $class_name ): bool|string
	{
		if( $root_namespace != 'JetShop' ) {
			return false;
		}

		if(str_starts_with($class_name, 'Core_')) {
			$class_name = str_replace( '_', DIRECTORY_SEPARATOR, substr($class_name, 4) );

			return SysConf_Path::getLibrary() . 'JetShopCore/' . $class_name . '.php';
		} else {
			$class_name = str_replace( '_', DIRECTORY_SEPARATOR, $class_name );

			return SysConf_Path::getApplication() . 'Classes/' . $class_name . '.php';
		}

	}
}
<?php
/**
 *
 * @copyright Copyright (c) 2011-2021 Miroslav Marek <mirek.marek@web-jet.cz>
 * @license http://www.php-jet.net/license/license.txt
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */

namespace JetStudio;

use Jet\Autoloader_Loader;
use Jet\SysConf_Path;

/**
 *
 */
class Autoloader_ProjectClasses extends Autoloader_Loader
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

		if( substr( $class_name, 0, 5 ) == 'Core_' ) {
			$class_name = str_replace( '_', DIRECTORY_SEPARATOR, substr( $class_name, 5 ) );

			return SysConf_Path::getLibrary() . 'JetShopCore/' . $class_name . '.php';

		} else {
			$class_name = str_replace( '_', DIRECTORY_SEPARATOR, $class_name );
			return SysConf_Path::getLibrary() . 'JetShop/' . $class_name . '.php';
		}

	}

}
<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */

use Jet\Autoloader_Loader;
use Jet\SysConf_Path;

return new class extends Autoloader_Loader
{

	public function getAutoloaderName() : string
	{
		return 'library/JetShop';
	}
	
	public function getScriptPath( string $class_name ): bool|string
	{
		
		if(!str_starts_with($class_name, 'JetShop\\')) {
			return false;
		}
		
		return SysConf_Path::getLibrary() . $this->classNameToPath( $class_name );
	}
};
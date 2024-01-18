<?php
namespace JetApplication;

use Jet\Application_Modules;
use Jet\Exception;


class Discounts {
	
	protected static ?Discounts_Manager $manager = null;
	
	public static function Manager() : Discounts_Manager
	{
		if(static::$manager) {
			return static::$manager;
		}
		
		$modules = Application_Modules::activatedModulesList();
		foreach($modules as $manifest) {
			$module = Application_Modules::moduleInstance( $manifest->getName() );
			
			if($module instanceof Discounts_Manager) {
				static::$manager = $module;
				
				return $module;
			}
		}
		
		throw new Exception('Mandatory service Discounts_Manager is not available');
		
	}
	
}

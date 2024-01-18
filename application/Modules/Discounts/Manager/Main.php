<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Discounts\Manager;

use Jet\Application_Modules;
use JetApplication\CashDesk;
use JetApplication\Discounts_Module;
use JetApplication\Discounts_Manager;


class Main extends Discounts_Manager
{
	protected static string $module_name_prefix = 'Discounts.';
	
	
	
	/**
	 * @return Discounts_Module[]
	 */
	public function getActiveModules() : array
	{
		$modules = [];
		
		$name_prefix = static::$module_name_prefix;
		
		foreach(Application_Modules::activatedModulesList() as $manifest) {
			if( str_starts_with( $manifest->getName(), $name_prefix ) ) {
				$module = Application_Modules::moduleInstance( $manifest->getName() );
				if($module instanceof Discounts_Module) {
					$module_name = $manifest->getName();
					$module_name = str_replace($name_prefix, '', $module_name);
					
					$modules[$module_name] = $module;
					
				}
			}
		}
		
		return $modules;
	}
	
	public function getActiveModule( string $module ) : ?Discounts_Module
	{
		$modules = static::getActiveModules();
		if(!isset($modules[$module])) {
			return null;
		}
		
		return $modules[$module];
	}
	
	/**
	 * @param CashDesk $cash_desk
	 */
	public function generateDiscounts( CashDesk $cash_desk ): void
	{
		foreach($this->getActiveModules() as $dm) {
			$dm->generateDiscounts( $cash_desk );
		}
	}
	
	public function checkDiscounts( CashDesk $cash_desk ): void
	{
		foreach($this->getActiveModules() as $dm) {
			$dm->checkDiscounts( $cash_desk );
		}
	}
}
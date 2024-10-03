<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Discounts\Manager;

use JetApplication\CashDesk;
use JetApplication\Discounts_Module;
use JetApplication\Discounts_Manager;
use JetApplication\Managers;


class Main extends Discounts_Manager
{
	protected static string $module_name_prefix = 'Discounts.';
	
	
	
	/**
	 * @return Discounts_Module[]
	 */
	public function getActiveModules() : array
	{
		$_modules =  Managers::findManagers( Discounts_Module::class, static::$module_name_prefix );
		$modules = [];
		
		foreach($_modules as $name => $module) {
			$name = str_replace( static::$module_name_prefix, '', $name );
			$modules[$name] = $module;
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
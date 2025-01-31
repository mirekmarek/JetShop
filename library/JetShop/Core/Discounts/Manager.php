<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;



use Jet\Application_Module;
use JetApplication\Discounts_Module;
use JetApplication\CashDesk;


abstract class Core_Discounts_Manager extends Application_Module {
	
	/**
	 * @return Discounts_Module[]
	 */
	abstract public function getActiveModules() : array;
	
	abstract public function getActiveModule( string $module ) : ?Discounts_Module;
	
	abstract public function generateDiscounts( CashDesk $cash_desk ) : void;
	
	abstract public function checkDiscounts( CashDesk $cash_desk ) : void;
	
}
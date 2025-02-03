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
use JetApplication\Manager_MetaInfo;


#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_GENERAL,
	is_mandatory: true,
	name: 'Discounts',
	description: '',
	module_name_prefix: ''
)]
abstract class Core_Discounts_Manager extends Application_Module {
	
	/**
	 * @return Discounts_Module[]
	 */
	abstract public function getActiveModules() : array;
	
	abstract public function getActiveModule( string $module ) : ?Discounts_Module;
	
	abstract public function generateDiscounts( CashDesk $cash_desk ) : void;
	
	abstract public function checkDiscounts( CashDesk $cash_desk ) : void;
	
}
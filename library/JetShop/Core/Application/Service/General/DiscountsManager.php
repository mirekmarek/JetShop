<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;



use Jet\Application_Module;
use JetApplication\Application_Service_EShop_DiscountModule;
use JetApplication\Application_Service_General;
use JetApplication\CashDesk;
use JetApplication\EShop;
use Jet\Application_Service_MetaInfo;


#[Application_Service_MetaInfo(
	group: Application_Service_General::GROUP,
	is_mandatory: true,
	name: 'Discounts',
	description: '',
	module_name_prefix: ''
)]
abstract class Core_Application_Service_General_DiscountsManager extends Application_Module {
	
	/**
	 * @return Application_Service_EShop_DiscountModule[]
	 */
	abstract public function getActiveModules( EShop $eshop ) : array;
	
	abstract public function getActiveModuleByInterface( string $interface_class_name, ?EShop $eshop=null ) : ?Application_Service_EShop_DiscountModule;
	
	abstract public function getActiveModuleByCode( string $code, ?EShop $eshop=null ) : ?Application_Service_EShop_DiscountModule;
	
	abstract public function generateDiscounts( CashDesk $cash_desk ) : void;
	
	abstract public function checkDiscounts( CashDesk $cash_desk ) : void;
	
}
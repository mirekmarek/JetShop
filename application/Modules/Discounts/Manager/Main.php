<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Discounts\Manager;


use JetApplication\CashDesk;
use JetApplication\Application_Service_EShop_DiscountModule;
use JetApplication\Application_Service_General_DiscountsManager;
use JetApplication\EShop;
use JetApplication\Application_Service_EShop;
use JetApplication\EShops;

class Main extends Application_Service_General_DiscountsManager
{
	
	/**
	 * @return Application_Service_EShop_DiscountModule[]
	 */
	public function getActiveModules( EShop $eshop ) : array
	{
		return Application_Service_EShop::DiscountModules( $eshop );
	}
	
	public function getActiveModuleByCode( string $code, ?EShop $eshop=null ) : ?Application_Service_EShop_DiscountModule
	{
		$eshop = $eshop?:EShops::getCurrent();
		
		return static::getActiveModules( $eshop )[$code]?? null;
	}
	
	public function getActiveModuleByInterface( string $interface_class_name, ?EShop $eshop = null ): ?Application_Service_EShop_DiscountModule
	{
		$eshop = $eshop?:EShops::getCurrent();
		
		$module = Application_Service_Eshop::list($eshop)->get( $interface_class_name );
		if(!$module) {
			return null;
		}
		
		foreach($this->getActiveModules($eshop) as $m) {
			if($m->getModuleManifest()->getName()==$module->getModuleManifest()->getName()) {
				return $m;
			}
		}
		
		return null;
	}
	
	
	/**
	 * @param CashDesk $cash_desk
	 */
	public function generateDiscounts( CashDesk $cash_desk ): void
	{
		foreach($this->getActiveModules( $cash_desk->getEshop() ) as $dm) {
			$dm->generateDiscounts( $cash_desk );
		}
	}
	
	public function checkDiscounts( CashDesk $cash_desk ): void
	{
		foreach($this->getActiveModules( $cash_desk->getEshop() ) as $dm) {
			$dm->checkDiscounts( $cash_desk );
		}
	}
	
}
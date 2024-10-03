<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Shop\CashDesk;

use Jet\Application_Module;
use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\Availabilities;
use JetApplication\ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
use JetApplication\Pricelists;
use JetApplication\Shop_Managers_CashDesk;
use JetApplication\CashDesk as Application_CashDesk;
use JetApplication\Shop_ModuleUsingTemplate_Interface;
use JetApplication\Shop_ModuleUsingTemplate_Trait;
use JetApplication\ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\Shops;

/**
 *
 */
class Main extends Application_Module implements
	Shop_Managers_CashDesk,
	Shop_ModuleUsingTemplate_Interface,
	Admin_ControlCentre_Module_Interface,
	ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface
{
	use Shop_ModuleUsingTemplate_Trait;
	use Admin_ControlCentre_Module_Trait;
	use ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
	
	
	protected ?CashDesk $cash_desk = null;
	
	
	public function getCashDesk() : Application_CashDesk
	{
		if(!$this->cash_desk) {
			/**
			 * @var Config_PerShop $config
			 */
			$config = $this->getShopConfig( Shops::getCurrent() );
			$this->cash_desk = new CashDesk(
				$config,
				Shops::getCurrent(),
				Pricelists::getCurrent(),
				Availabilities::getCurrent()
			);
		}
		
		$this->cash_desk->checkCurrentCustomer();
		$this->cash_desk->getDiscounts();
		
		return $this->cash_desk;
	}
	
	public function getControlCentreGroup(): string
	{
		return Admin_ControlCentre::GROUP_MAIN;
	}
	
	
	public function getControlCentreTitle(): string
	{
		return 'Cash Desk';
	}
	
	public function getControlCentreIcon(): string
	{
		return 'cash-register';
	}
	
	public function getControlCentrePerShopMode() : bool
	{
		return true;
	}
	
	public function getControlCentrePriority(): int
	{
		return 10;
	}
	
}
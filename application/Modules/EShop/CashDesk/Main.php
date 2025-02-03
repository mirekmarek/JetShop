<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\CashDesk;

use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\Availabilities;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
use JetApplication\Pricelists;
use JetApplication\EShop_Managers_CashDesk;
use JetApplication\CashDesk as Application_CashDesk;
use JetApplication\EShop_ModuleUsingTemplate_Interface;
use JetApplication\EShop_ModuleUsingTemplate_Trait;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\EShops;


class Main extends EShop_Managers_CashDesk implements
	EShop_ModuleUsingTemplate_Interface,
	Admin_ControlCentre_Module_Interface,
	EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface
{
	use EShop_ModuleUsingTemplate_Trait;
	use Admin_ControlCentre_Module_Trait;
	use EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
	
	
	protected ?CashDesk $cash_desk = null;
	
	
	public function getCashDesk() : Application_CashDesk
	{
		if(!$this->cash_desk) {
			/**
			 * @var Config_PerShop $config
			 */
			$config = $this->getEshopConfig( EShops::getCurrent() );
			$this->cash_desk = new CashDesk(
				$config,
				EShops::getCurrent(),
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
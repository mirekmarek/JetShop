<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\CalendarManager;

use Jet\Application_Module;
use Jet\Data_DateTime;
use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\Calendar_Manager;
use JetApplication\ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
use JetApplication\ShopConfig_ModuleConfig_PerShop;
use JetApplication\Shops;
use JetApplication\Shops_Shop;

/**
 *
 */
class Main extends Application_Module implements
										Calendar_Manager,
										ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface,
										Admin_ControlCentre_Module_Interface
{
	use ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
	use Admin_ControlCentre_Module_Trait;
	
	protected function getConfig( ?Shops_Shop $shop = null ) : Config_PerShop|ShopConfig_ModuleConfig_PerShop
	{
		if(!$shop) {
			$shop = Shops::getCurrent();
		}
		
		return $this->getShopConfig( $shop );
	}
	
	public function getNextBusinessDate( ?Shops_Shop $shop = null, int $number_of_working_days = 1, Data_DateTime|string $start_date = 'now', bool $use_holydays = true ): Data_DateTime
	{
		return Calendar::get( $this->getConfig($shop) )->getNextBusinessDate( $number_of_working_days, $start_date, $use_holydays );
	}
	
	public function getPrevBusinessDate( ?Shops_Shop $shop = null, int $number_of_working_days = 1, Data_DateTime|string $start_date = 'now', bool $use_holydays = true ): Data_DateTime
	{
		return Calendar::get( $this->getConfig($shop) )->getPrevBusinessDate( $number_of_working_days, $start_date, $use_holydays );
	}
	
	public function calcBusinessDaysOffset( Shops_Shop $shop, int $number_of_working_days, Data_DateTime|string $start_date = 'now', bool $use_holydays = true ): int
	{
		return Calendar::get( $this->getConfig($shop) )->calcBusinessDaysOffset( $number_of_working_days, $start_date, $use_holydays );
	}
	
	public function calcBusinessDaysSubset( Shops_Shop $shop, int $number_of_working_days, Data_DateTime|string $start_date = 'now', bool $use_holydays = true ): int
	{
		return Calendar::get( $this->getConfig($shop) )->calcBusinessDaysSubset( $number_of_working_days, $start_date, $use_holydays );
	}
	
	public function howManyWorkingDays( Shops_Shop $shop, Data_DateTime|string $from, Data_DateTime|string $till, bool $use_holydays = true ): int
	{
		return Calendar::get( $this->getConfig($shop) )->howManyWorkingDays( $from, $till, $use_holydays );
	}
	
	public function getControlCentreGroup(): string
	{
		return Admin_ControlCentre::GROUP_MAIN;
	}
	
	public function getControlCentreTitle(): string
	{
		return 'Calendar';
	}
	
	public function getControlCentreIcon(): string
	{
		return 'calendar-days';
	}
	
	public function getControlCentrePriority(): int
	{
		return 1;
	}
	
	public function getControlCentrePerShopMode(): bool
	{
		return true;
	}
}
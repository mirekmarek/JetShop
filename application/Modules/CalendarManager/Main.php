<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\CalendarManager;

use Jet\Data_DateTime;
use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\Calendar_Manager;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
use JetApplication\EShopConfig_ModuleConfig_PerShop;
use JetApplication\EShops;
use JetApplication\EShop;


class Main extends Calendar_Manager implements
										EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface,
										Admin_ControlCentre_Module_Interface
{
	use EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
	use Admin_ControlCentre_Module_Trait;
	
	protected function getConfig( ?EShop $eshop = null ) : Config_PerShop|EShopConfig_ModuleConfig_PerShop
	{
		if(!$eshop) {
			$eshop = EShops::getCurrent();
		}
		
		return $this->getEshopConfig( $eshop );
	}
	
	public function getNextBusinessDate( ?EShop $eshop = null, int $number_of_working_days = 1, Data_DateTime|string $start_date = 'now', bool $use_holydays = true ): Data_DateTime
	{
		return Calendar::get( $this->getConfig($eshop) )->getNextBusinessDate( $number_of_working_days, $start_date, $use_holydays );
	}
	
	public function getPrevBusinessDate( ?EShop $eshop = null, int $number_of_working_days = 1, Data_DateTime|string $start_date = 'now', bool $use_holydays = true ): Data_DateTime
	{
		return Calendar::get( $this->getConfig($eshop) )->getPrevBusinessDate( $number_of_working_days, $start_date, $use_holydays );
	}
	
	public function calcBusinessDaysOffset( EShop $eshop, int $number_of_working_days, Data_DateTime|string $start_date = 'now', bool $use_holydays = true ): int
	{
		return Calendar::get( $this->getConfig($eshop) )->calcBusinessDaysOffset( $number_of_working_days, $start_date, $use_holydays );
	}
	
	public function calcBusinessDaysSubset( EShop $eshop, int $number_of_working_days, Data_DateTime|string $start_date = 'now', bool $use_holydays = true ): int
	{
		return Calendar::get( $this->getConfig($eshop) )->calcBusinessDaysSubset( $number_of_working_days, $start_date, $use_holydays );
	}
	
	public function howManyWorkingDays( EShop $eshop, Data_DateTime|string $from, Data_DateTime|string $till, bool $use_holydays = true ): int
	{
		return Calendar::get( $this->getConfig($eshop) )->howManyWorkingDays( $from, $till, $use_holydays );
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
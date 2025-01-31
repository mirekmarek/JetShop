<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\NumberSeriesManager;


use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\NumberSeries_Counter_Day;
use JetApplication\NumberSeries_Counter_Month;
use JetApplication\NumberSeries_Counter_Total;
use JetApplication\NumberSeries_Counter_Year;
use JetApplication\EShopEntity_HasNumberSeries_Interface;
use JetApplication\NumberSeries_Manager;


class Main extends NumberSeries_Manager implements Admin_ControlCentre_Module_Interface
{
	use Admin_ControlCentre_Module_Trait;
	
	public function generateNumber( EShopEntity_HasNumberSeries_Interface $entity ): string
	{
		$config = new EntityConfig( $entity->getNumberSeriesEntityType(), $entity->getNumberSeriesEntityShop() );
		
		/**
		 * @var NumberSeries_Counter_Day|NumberSeries_Counter_Month|NumberSeries_Counter_Year|NumberSeries_Counter_Total $counter
		 */
		$counter = $config->getCounterClass();
		$pad_length = $config->getPadLength();
		$prefix = $config->getPrefix();
		
		$number = $counter::generate( $entity, $pad_length );
		
		
		$number = $prefix.$number;
		
		return $number;
	}
	
	
	public function getControlCentreGroup(): string
	{
		return Admin_ControlCentre::GROUP_MAIN;
	}
	
	public function getControlCentreTitle(): string
	{
		return 'Number Series';
	}
	
	public function getControlCentreIcon(): string
	{
		return 'arrow-down-1-9';
	}
	
	public function getControlCentrePriority(): int
	{
		return 99;
	}
	
	public function getControlCentrePerShopMode(): bool
	{
		return false;
	}
}
<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ControlCentreModule\Modules;


use Jet\Application_Module;
use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;


class Main extends Application_Module implements Admin_ControlCentre_Module_Interface
{
	use Admin_ControlCentre_Module_Trait;
	
	public function getControlCentreGroup(): string
	{
		return Admin_ControlCentre::GROUP_SYSTEM;
	}
	
	public function getControlCentreTitle(): string
	{
		return 'Modules';
	}
	
	public function getControlCentreIcon(): string
	{
		return 'boxes-stacked';
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
<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ControlCentreModule\Pages;


use Jet\Application_Module;
use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;


class Main extends Application_Module implements Admin_ControlCentre_Module_Interface
{
	use Admin_ControlCentre_Module_Trait;
	
	public function getControlCentreGroup(): string
	{
		return Admin_ControlCentre::GROUP_MAIN;
	}
	
	public function getControlCentreTitle(): string
	{
		return 'Pages';
	}
	
	public function getControlCentreIcon(): string
	{
		return 'file-code';
	}
	
	public function getControlCentrePriority(): int
	{
		return 99;
	}
	
	public function getControlCentrePerShopMode(): bool
	{
		return true;
	}
}
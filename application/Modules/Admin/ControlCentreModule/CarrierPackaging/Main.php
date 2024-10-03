<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\ControlCentreModule\CarrierPackaging;

use Jet\Application_Module;
use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;

/**
 *
 */
class Main extends Application_Module implements Admin_ControlCentre_Module_Interface
{
	use Admin_ControlCentre_Module_Trait;
	
	public function getControlCentreGroup(): string
	{
		return Admin_ControlCentre::GROUP_DELIVERY;
	}
	
	public function getControlCentreTitle(): string
	{
		return 'Carrier Packaging definition';
	}
	
	public function getControlCentreIcon(): string
	{
		return 'box';
	}
	
	public function getControlCentrePriority(): int
	{
		return 2;
	}
	
	public function getControlCentrePerShopMode(): bool
	{
		return false;
	}
}
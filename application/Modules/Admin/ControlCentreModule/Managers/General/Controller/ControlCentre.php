<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ControlCentreModule\Managers\General;

use JetApplication\Admin_ControlCentre_Module_Controller;
use JetApplication\Application_Service_General;


class Controller_ControlCentre extends Admin_ControlCentre_Module_Controller
{
	
	public function default_Action() : void
	{
		$list = Application_Service_General::list();
		$list->handleControlCentre();
		$this->view->setVar('services', $list);
		
		$this->output('default');
	}
}
<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ControlCentreModule\Managers\Admin;

use JetApplication\Admin_ControlCentre_Module_Controller;
use JetApplication\Application_Service_Admin;


class Controller_ControlCentre extends Admin_ControlCentre_Module_Controller
{

	public function default_Action() : void
	{
		$list = Application_Service_Admin::list();
		$list->handleControlCentre();
		$this->view->setVar('services', $list);
		
		$this->output('default');
	}
}
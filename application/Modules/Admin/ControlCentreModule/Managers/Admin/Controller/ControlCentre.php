<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ControlCentreModule\Managers\Admin;


use Jet\Exception;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Logger;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_ControlCentre_Module_Controller;
use JetApplication\Admin_Managers;


class Controller_ControlCentre extends Admin_ControlCentre_Module_Controller
{

	/**
	 *
	 */
	public function default_Action() : void
	{
		$managers = Admin_Managers::getRegisteredManagers();
		$config = Admin_Managers::getConfig();
		
		$this->view->setVar('managers', $managers);
		$this->view->setVar('config', $config);
		
		$POST = Http_Request::POST();
		
		if($POST->getString('action')=='save') {
			$interface_class_name = $POST->getString('interface_class_name');
			$manager = $POST->getString('manager');
			
			$ok = true;
			try {
				Admin_Managers::setManagerConfig( $interface_class_name, $manager );
				Admin_Managers::saveCfg();
				
				Logger::info(
					event: 'manager_set',
					event_message: 'Manager '.$interface_class_name.' has been set to '.$manager ,
					context_object_id: $interface_class_name,
					context_object_data: [
						'interface_class_name' => $interface_class_name,
						'manager' => $manager
					]
				);
				
			} catch( Exception $e ) {
				$ok = false;
				UI_messages::danger( Tr::_('Error during configuration saving: ').$e->getMessage(), context: 'CC' );
			}
			
			if($ok) {
				UI_messages::success( Tr::_('Configuration has been saved'), context: 'CC' );
			}
			
			Http_Headers::reload();
			
		}
		
		$this->output('default');
	}
}
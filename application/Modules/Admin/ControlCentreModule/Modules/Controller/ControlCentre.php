<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ControlCentreModule\Modules;


use Jet\Application_Modules;
use Error;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Logger;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_ControlCentre_Module_Controller;



class Controller_ControlCentre extends Admin_ControlCentre_Module_Controller
{

	/**
	 *
	 */
	public function default_Action() : void
	{
		$modules = Application_Modules::allModulesList();
		
		$GET = Http_Request::GET();
		
		if( ($module=$GET->getString('install')) ) {
			$ok = true;
			try {
				Application_Modules::installModule( $module );
				Logger::info(
					event: 'module_installed',
					event_message: 'Module '. $module .' installed successfully.',
					context_object_id: $module
				);
			} catch( Error $e ) {
				$ok = false;
				UI_messages::danger(
					Tr::_('Error during module installation: %error%', ['error' => $e->getMessage()]),
					'CC'
				);
			}
			
			if($ok) {
				UI_messages::success(
					Tr::_('Module %module% has been installed', ['module'=>$module]),
					'CC'
				);
			}
			Http_Headers::reload(unset_GET_params: ['install']);
		}
		
		
		if( ($module=$GET->getString('install_activate')) ) {
			$ok = true;
			try {
				Application_Modules::installModule( $module );
				Logger::info(
					event: 'module_installed',
					event_message: 'Module '. $module .' installed successfully.',
					context_object_id: $module
				);
				
				Application_Modules::activateModule( $module );
				Logger::info(
					event: 'module_activated',
					event_message: 'Module '. $module .' activated',
					context_object_id: $module
				);
				
			} catch( Error $e ) {
				$ok = false;
				UI_messages::danger(
					Tr::_('Error during module installation: %error%', ['error' => $e->getMessage()]),
					'CC'
				);
			}
			
			if($ok) {
				UI_messages::success(
					Tr::_('Module %module% has been installed and activated', ['module'=>$module]),
					'CC'
				);
			}
			Http_Headers::reload(unset_GET_params: ['install_activate']);
		}
		
		
		if( ($module=$GET->getString('deactivate')) ) {
			$ok = true;
			try {
				Application_Modules::deactivateModule( $module );
				Logger::info(
					event: 'module_deactivated',
					event_message: 'Module '. $module .' deactivated',
					context_object_id: $module
				);
				
			} catch( Error $e ) {
				$ok = false;
				UI_messages::danger(Tr::_('Error during module deactivation: %error%', ['error' => $e->getMessage()]));
			}
			
			if($ok) {
				UI_messages::success(
					Tr::_('Module %module% has been deactivated', ['module'=>$module]),
					'CC'
				);
			}
			Http_Headers::reload(unset_GET_params: ['deactivate']);
		}
		
		
		
		if( ($module=$GET->getString('activate')) ) {
			$ok = true;
			try {
				Application_Modules::activateModule( $module );
				Logger::info(
					event: 'module_activated',
					event_message: 'Module '. $module .' activated',
					context_object_id: $module
				);
				
			} catch( Error $e ) {
				$ok = false;
				UI_messages::danger(
					Tr::_('Error during module activation: %error%', ['error' => $e->getMessage()]),
					'CC'
				);
			}
			
			if($ok) {
				UI_messages::success(
					Tr::_('Module %module% has been activated', ['module'=>$module]),
					'CC'
				);
			}
			Http_Headers::reload(unset_GET_params: ['activate']);
		}
		
		
		if( ($module=$GET->getString('uninstall')) ) {
			$ok = true;
			try {
				Application_Modules::uninstallModule( $module );
				
				Logger::info(
					event: 'module_uninstalled',
					event_message: 'Module '. $module .' uninstalled',
					context_object_id: $module
				);
				
			} catch( Error $e ) {
				$ok = false;
				UI_messages::danger(
					Tr::_('Error during module uninstallation: %error%', ['error' => $e->getMessage()]),
					'CC'
				);
			}
			
			if($ok) {
				UI_messages::success(
					Tr::_('Module %module% has been uninstalled', ['module'=>$module]),
					'CC'
				);
			}
			Http_Headers::reload(unset_GET_params: ['uninstall']);
		}
		
		$this->view->setVar('modules', $modules);
		
		$this->output('default');
	}
}
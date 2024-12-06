<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\CalendarManager;

use Exception;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_ControlCentre_Module_Controller;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;

class Controller_ControlCentre extends Admin_ControlCentre_Module_Controller {
	
	public function default_Action() : void
	{
		$eshop = $this->getEshop();
		
		/**
		 * @var EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface $module
		 */
		$module = $this->getModule();
		
		$config = $module->getEshopConfig( $eshop );
		
		$save = function() use ($config) {
			$ok = true;
			try {
				$config->saveConfigFile();
			} catch( Exception $e ) {
				$ok = false;
				UI_messages::danger( Tr::_('Error during configuration saving: ').$e->getMessage(), context: 'CC' );
			}
			
			if($ok) {
				UI_messages::success( Tr::_('Configuration has been saved'), context: 'CC' );
			}
			
		};
		
		$GET = Http_Request::GET();
		
		if($GET->exists('action')) {
			switch($GET->getString('action')) {
				case 'add_national_holiday':
					if( $config->addNationalHoliday( $GET->getString('day') ) ) {
						$save();
					}
					Http_Headers::reload(unset_GET_params: ['action', 'day']);
					break;
				case 'remove_national_holiday':
					if( $config->removeNationalHoliday( $GET->getString('day') ) ) {
						$save();
					}
					Http_Headers::reload(unset_GET_params: ['action', 'day']);
					break;
				case 'add_custom_free_day':
					if( $config->addCustomFreeDay( $GET->getString('day') )  ) {
						$save();
					}
					Http_Headers::reload(unset_GET_params: ['action', 'day']);
					break;
				case 'remove_custom_free_day':
					if( $config->removeCustomFreeDay( $GET->getString('day') ) ) {
						$save();
					}
					Http_Headers::reload(unset_GET_params: ['action', 'day']);
					break;
			}
		}
		
		$form = $config->createForm('config_form');
		
		if( $form->catch() ) {
			$save();
			
			Http_Headers::reload();
		}
		
		$this->view->setVar('config', $config);
		$this->view->setVar('form', $form);
		
		$this->output('control-centre/default');
	}
}
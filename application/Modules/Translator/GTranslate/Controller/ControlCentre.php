<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Translator\GTranslate;

use Exception;
use Jet\Http_Headers;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_ControlCentre_Module_Controller;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_General_Interface;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;

class Controller_ControlCentre extends Admin_ControlCentre_Module_Controller {
	
	public function default_Action() : void
	{
		$eshop = $this->getEshop();
		/**
		 * @var EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface|EShopConfig_ModuleConfig_ModuleHasConfig_General_Interface $module
		 */
		$module = $this->getModule();
		
		$main_config = $module->getGeneralConfig();
		
		
		$form = $main_config->createForm('config_form');
		
		if( $form->catch() ) {
			
			$ok = true;
			try {
				$main_config->saveConfigFile();
			} catch( Exception $e ) {
				$ok = false;
				UI_messages::danger( Tr::_('Error during configuration saving: ').$e->getMessage(), context: 'CC' );
			}
			
			if($ok) {
				UI_messages::success( Tr::_('Configuration has been saved'), context: 'CC' );
			}
			
			Http_Headers::reload();
		}
		
		$this->view->setVar('config', $main_config);
		$this->view->setVar('form', $form);
		
		$this->output('control-centre/default');
	}
}
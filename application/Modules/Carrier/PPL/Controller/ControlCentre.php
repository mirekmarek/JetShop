<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Carrier\PPL;

use Exception;
use Jet\Http_Headers;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_ControlCentre_Module_Controller;
use JetApplication\ShopConfig_ModuleConfig_ModuleHasConfig_General_Interface;
use JetApplication\ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;

class Controller_ControlCentre extends Admin_ControlCentre_Module_Controller {
	
	public function default_Action() : void
	{
		$shop = $this->getShop();
		/**
		 * @var ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface|ShopConfig_ModuleConfig_ModuleHasConfig_General_Interface $module
		 */
		$module = $this->getModule();
		
		$main_config = $module->getGeneralConfig();
		$shop_config = $module->getShopConfig( $shop );
		
		$form = $shop_config->createForm('config_form');
		foreach( $main_config->createForm('config_form')->getFields() as $field ) {
			$form->addField( $field );
		}
		
		if( $form->catch() ) {
			
			$ok = true;
			try {
				$main_config->saveConfigFile();
				$shop_config->saveConfigFile();
			} catch( Exception $e ) {
				$ok = false;
				UI_messages::danger( Tr::_('Error during configuration saving: ').$e->getMessage(), context: 'CC' );
			}
			
			if($ok) {
				UI_messages::success( Tr::_('Configuration has been saved'), context: 'CC' );
			}
			
			Http_Headers::reload();
		}
		
		$this->view->setVar('config', $shop_config);
		$this->view->setVar('form', $form);
		
		$this->output('control-centre/default');
	}
}
<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EMailMarketing\EComail;

use Exception;
use Jet\Http_Headers;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_ControlCentre_Module_Controller;

class Controller_ControlCentre extends Admin_ControlCentre_Module_Controller {
	
	protected Config_PerShop $config;
	
	public function default_Action() : void
	{
		$eshop = $this->getEshop();
		/**
		 * @var Main $module
		 * @var Config_PerShop $config
		 */
		$module = $this->getModule();
		
		$config = $module->getEshopConfig( $eshop );
		$this->config = $config;
		$this->handleMainCfgForm();
		
		$this->view->setVar('config', $config);
		
		
		$this->output('control-centre/default');
	}
	
	protected function saveConfig() : void
	{
		$ok = true;
		try {
			$this->config->saveConfigFile();
		} catch( Exception $e ) {
			$ok = false;
			UI_messages::danger( Tr::_('Error during configuration saving: ').$e->getMessage(), context: 'CC' );
		}
		
		if($ok) {
			UI_messages::success( Tr::_('Configuration has been saved'), context: 'CC' );
		}
		
	}
	
	public function handleMainCfgForm() : void
	{
		
		$form = $this->config->createForm('config_form');
		
		if( $form->catch() ) {
			$this->saveConfig();
			Http_Headers::reload();
		}
		
		$this->view->setVar('form', $form);
	}
	
}
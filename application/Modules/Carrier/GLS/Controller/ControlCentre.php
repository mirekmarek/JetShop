<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Carrier\GLS;


use Exception;
use Jet\Http_Headers;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_ControlCentre_Module_Controller;

class Controller_ControlCentre extends Admin_ControlCentre_Module_Controller {

	public function default_Action() : void
	{
		$eshop = $this->getEshop();
		/**
		 * @var Main $module
		 */
		$module = $this->getModule();
		
		$eshop_config = $module->getEshopConfig( $eshop );
		
		$form = $eshop_config->getForm( $module, $eshop );
		
		if( $form->catch() ) {

			$ok = true;
			try {
				$eshop_config->saveConfigFile();
			} catch( Exception $e ) {
				$ok = false;
				UI_messages::danger( Tr::_('Error during configuration saving: ').$e->getMessage(), context: 'CC' );
			}
			
			if($ok) {
				UI_messages::success( Tr::_('Configuration has been saved'), context: 'CC' );
			}
			
			Http_Headers::reload();
		}
		
		$this->view->setVar('config', $eshop_config);
		$this->view->setVar('form', $form);
		
		$this->output('control-centre/default');
	}
}
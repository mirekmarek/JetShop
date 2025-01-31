<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\CashDesk;


use Exception;
use Jet\Form_Field_Float;
use Jet\Http_Headers;
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
		
		$form = $config->createForm('config_form');
		/**
		 * @var Form_Field_Float $map_center_lat
		 * @var Form_Field_Float $map_center_lon
		 */
		$map_center_lat = $form->field('map_center_lat');
		$map_center_lat->setPlaces(10);
		$map_center_lon = $form->field('map_center_lon');
		$map_center_lon->setPlaces(10);
		
		if( $form->catch() ) {

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
			
			Http_Headers::reload();
		}
		
		$this->view->setVar('config', $config);
		$this->view->setVar('form', $form);
		
		$this->output('control-centre/default');
	}
}
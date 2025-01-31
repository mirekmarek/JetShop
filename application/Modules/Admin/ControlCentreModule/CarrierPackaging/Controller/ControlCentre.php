<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ControlCentreModule\CarrierPackaging;


use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_ControlCentre_Module_Controller;
use JetApplication\Carrier;
use JetApplication\Carrier_Packaging;



class Controller_ControlCentre extends Admin_ControlCentre_Module_Controller
{
	
	public function default_Action() : void
	{
		
		$GET = Http_Request::GET();
		
		$carrier = null;
		$carrier_service = null;
		
		$carrier_code = $GET->getString('carrier', valid_values: array_keys(Carrier::getScope()) );
		if($carrier_code) {
			$carrier = Carrier::get( $carrier_code );
			$carrier_service_code = $GET->getString('service', valid_values: array_keys($carrier->getServicesList()) );
			
			if( $carrier_service_code ) {
				$carrier_service = $carrier->getService( $carrier_service_code );
			}
		}
		
		$this->view->setVar( 'carrier', $carrier );
		$this->view->setVar( 'carrier_service', $carrier_service );

		
		if($carrier_service) {

			if( $GET->exists('add') ) {
				$new_packaging = new Carrier_Packaging();
				$new_packaging->setCarrierService( $carrier_service );
				
				if( $new_packaging->getAddForm()->catch() ) {
					$new_packaging->save();
					UI_messages::success( Tr::_('Configuration has been saved'), context: 'CC' );
					Http_Headers::reload();
				}
				
				$this->view->setVar('new_packaging', $new_packaging);
			}
			
			
			$selected_packaging_id = $GET->getString('packaging', default_value: '', valid_values: array_keys( $carrier_service->getAvailablePackaging() ));
			
			if($selected_packaging_id) {
				$selected_packaging = Carrier_Packaging::get($carrier_service, $selected_packaging_id);
				
				if($GET->exists('delete')) {
					$selected_packaging->delete();
					Http_Headers::reload(unset_GET_params: ['packaging', 'delete']);
				}
				
				
				if( $selected_packaging->getEditForm()->catch() ) {
					$selected_packaging->save();
					UI_messages::success( Tr::_('Configuration has been saved'), context: 'CC' );
					Http_Headers::reload();
				}
				
				$this->view->setVar('selected_packaging', $selected_packaging);
			}
		}
		
		
		
		$this->output('default');
	}
}
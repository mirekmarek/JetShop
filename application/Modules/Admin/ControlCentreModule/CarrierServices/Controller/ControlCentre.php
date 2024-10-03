<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\ControlCentreModule\CarrierServices;

use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_ControlCentre_Module_Controller;
use JetApplication\Carrier;
use JetApplication\Carrier_Service;


/**
 *
 */
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
		

		if($carrier) {
			if( $GET->exists('add') ) {
				$new_carrier_service = new Carrier_Service();
				$new_carrier_service->setCarrier( $carrier );
				
				if( $new_carrier_service->getAddForm()->catch() ) {
					$new_carrier_service->save();
					UI_messages::success( Tr::_('Configuration has been saved'), context: 'CC' );
					Http_Headers::reload();
				}
				
				$this->view->setVar('new_carrier_service', $new_carrier_service);
			}
			
			if($carrier_service) {
				if($GET->exists('delete')) {
					$carrier_service->delete();
					Http_Headers::reload(unset_GET_params: ['service', 'delete']);
				}
				
				
				if( $carrier_service->getEditForm()->catch() ) {
					$carrier_service->save();
					UI_messages::success( Tr::_('Configuration has been saved'), context: 'CC' );
					Http_Headers::reload();
				}
			}
		}
		
		
		
		
		
		$this->output('default');
	}
}
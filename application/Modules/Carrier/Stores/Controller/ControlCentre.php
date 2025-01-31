<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Carrier\Stores;


use Jet\Http_Headers;
use Jet\Http_Request;
use JetApplication\Admin_ControlCentre_Module_Controller;
use JetApplication\Carrier_DeliveryPoint;

class Controller_ControlCentre extends Admin_ControlCentre_Module_Controller {

	public function default_Action() : void
	{
		$eshop = $this->getEshop();
		
		/**
		 * @var Main $module
		 */
		$module = $this->getModule();
		
		$new_point = null;
		$selected_point = null;
		
		if(
			($point_id = Http_Request::GET()->getInt('point')) &&
			$selected_point = Carrier_DeliveryPoint::load([
				'id' => $point_id,
				'AND',
				'carrier_code' => $module->getCode(),
				'AND',
				'point_locale' => $eshop->getLocale()
			])
		) {
			if( $selected_point->getEditForm()->catch() ) {
				
				$selected_point->save();
				
				Http_Headers::reload();
			}
		} else {
			if(Http_Request::GET()->exists('new_point')) {
				$new_point = new Carrier_DeliveryPoint();
				$new_point->setPointLocale( $eshop->getLocale() );
				$new_point->setCarrier( $module );
				
				$new_point->addOpeningHours( 'monday', '', '' );
				$new_point->addOpeningHours( 'tuesday', '', '' );
				$new_point->addOpeningHours( 'wednesday', '', '' );
				$new_point->addOpeningHours( 'thursday', '', '' );
				$new_point->addOpeningHours( 'friday', '', '' );
				$new_point->addOpeningHours( 'saturday', '', '' );
				$new_point->addOpeningHours( 'sunday', '', '' );
				
				if($new_point->getAddForm()->catch()) {
					$new_point->save();
					Http_Headers::reload(['point'=>$new_point->getId()]);
				}
			}
		}
		
		$points = Carrier_DeliveryPoint::getPointList( $module, only_locale: $eshop->getLocale() );
		
		
		$this->view->setVar('points', $points);
		$this->view->setVar('new_point', $new_point);
		$this->view->setVar('selected_point', $selected_point);
		
		$this->output('control-centre/default');
	}
}
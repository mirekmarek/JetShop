<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Delivery\Methods;


use Jet\AJAX;
use Jet\Http_Headers;
use Jet\Http_Request;
use JetApplication\Admin_EntityManager_Controller;
use JetApplication\Carrier;
use JetApplication\Delivery_Method;

class Controller_Main extends Admin_EntityManager_Controller
{
	public function edit_main_Action(): void
	{
		$this->handleSetPrice();
		
		parent::edit_main_Action();
		
		$this->content->output(
			$this->view->render('edit/main/set-price')
		);
	}
	
	protected function setupRouter( string $action, string $selected_tab ): void
	{
		parent::setupRouter( $action, $selected_tab );
		
		$this->router->addAction('get_carrier_services', Main::ACTION_GET)
			->setResolver(function() use ($action, $selected_tab) {
				return $action=='get_carrier_services';
			});
		$this->router->addAction('get_carrier_dp_types', Main::ACTION_GET)
			->setResolver(function() use ($action, $selected_tab) {
				return $action=='get_carrier_dp_types';
			});
	}
	
	public function setupListing(): void
	{
		$this->listing_manager->addColumn( new Listing_Column_FreeLimit() );
		$this->listing_manager->addColumn( new Listing_Column_Price() );
		
		$this->listing_manager->setDefaultColumnsSchema([
			'id',
			'active_state',
			'internal_name',
			'internal_code',
			'internal_notes',
			Listing_Column_FreeLimit::KEY,
			Listing_Column_Price::KEY
		]);
	}
	
	
	public function handleSetPrice() : void
	{
		/**
		 * @var Delivery_Method $product
		 */
		$method = $this->current_item;
		
		if( ($set_price_form = $method->getSetPriceForm()) ) {
			$this->view->setVar('set_price_form', $set_price_form);
			if($set_price_form->catch()) {
				Http_Headers::reload();
			}
		}
		
	}
	
	public function get_carrier_services_Action() : void
	{
		$carrier_code = Http_Request::GET()->getString('carrier', valid_values: array_keys( Carrier::getScope() ));
		
		$res = [];
		
		if($carrier_code) {
			$carrier = Carrier::get( $carrier_code );
			
			$res = $carrier->getServicesList();
		}
		
		AJAX::commonResponse( $res );
		
	}
	
	public function get_carrier_dp_types_Action() : void
	{
		$carrier_code = Http_Request::GET()->getString('carrier', valid_values: array_keys( Carrier::getScope() ));
		
		$res = [];
		
		if($carrier_code) {
			$carrier = Carrier::get( $carrier_code );
			
			$res = $carrier->getDeliveryPointTypeOptions();
		}
		
		AJAX::commonResponse( $res );
		
	}

	
}
<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\CashDesk;


use Jet\AJAX;
use Jet\Application;
use Jet\Http_Request;
use JetApplication\Carrier_DeliveryPoint;

trait Controller_Main_Delivery {

	public function getControllerRouter_Delivery() : void
	{

		$GET = Http_Request::GET();
		$action = $GET->getString('action');

		$this->router->addAction('personal_takeover_get_map_data')->setResolver(function() use ($action) {
			return $action=='personal_takeover_get_map_data';
		});

		$this->router->addAction('select_delivery')->setResolver(function() use ($action) {
			return $action=='select_delivery';
		});
		$this->router->addAction('personal_takeover_show_point')->setResolver(function() use ($action) {
			return $action=='personal_takeover_show_point';
		});

		$this->router->addAction('personal_takeover_select_point')->setResolver(function() use ($action) {
			return $action=='personal_takeover_select_point';
		});

		$this->router->addAction('continue_to_payment')->setResolver(function() use ($action) {
			return $action=='continue_to_payment';
		});

		$this->router->addAction('back_to_delivery')->setResolver(function() use ($action) {
			return $action=='back_to_delivery';
		});
		
		$this->router->addAction('whisper_point')->setResolver(function() use ($action) {
			return $action=='whisper_point';
		});
		
		
	}


	public function personal_takeover_get_map_data_Action() : void
	{
		$only_method_codes = [];

		$GET = Http_Request::GET();

		if($GET->exists('methods')) {
			$only_method_codes = explode(',', $GET->getString('methods'));
		}

		AJAX::commonResponse( Carrier_DeliveryPoint::getMapData( only_method_ids: $only_method_codes ) );
	}

	public function personal_takeover_show_point_Action(): void
	{
		$GET = Http_Request::GET();
		$cash_desk = $this->cash_desk;
		
		if(
			($point = $cash_desk->getDeliveryPointByCode( $GET->getString('id'), $method ))
		) {
			$this->view->setVar('point', $point);
			$this->view->setVar('delivery_method', $method);
			
			echo $this->view->render('delivery/method/personal_takeover/point_detail');
		}
		
		Application::end();
	}

	public function personal_takeover_select_point_Action() : void
	{
		$response = new Controller_Main_Response( $this );
		$cash_desk = $this->cash_desk;
		$GET = Http_Request::GET();
		
		if(
			($point = $cash_desk->getDeliveryPointByCode( $GET->getString('id'), $method ))
		) {

			if(!$cash_desk->selectPersonalTakeoverDeliveryPoint( $method, $point )) {
				$response->error();
			}
			
			$response->addSnippet( 'overview' );
			$response->addSnippet( 'delivery' );
			
			$response->response();
			
 		} else {
			$response->error();
		}
		


	}

	public function select_delivery_Action() : void
	{

		$response = new Controller_Main_Response( $this );
		$cash_desk = $this->cash_desk;
		$GET = Http_Request::GET();

		if(!$cash_desk->selectDeliveryMethod( $GET->getString('method') )) {
			$response->error();
		}

		$response->addSnippet( 'overview' );
		$response->addSnippet( 'delivery' );

		$response->response();
	}

	public function continue_to_payment_Action() : void
	{

		$response = new Controller_Main_Response( $this );
		$cash_desk = $this->cash_desk;

		$cash_desk->setCurrentStep( CashDesk::STEP_PAYMENT );

		$response->addSnippet( 'overview' );
		$response->addSnippet( 'delivery' );
		$response->addSnippet( 'payment' );


		$response->response();

	}

	public function back_to_delivery_Action() : void
	{

		$response = new Controller_Main_Response( $this );
		$cash_desk = $this->cash_desk;

		$cash_desk->setCurrentStep( CashDesk::STEP_DELIVERY );

		$response->addSnippet( 'overview' );
		$response->addSnippet( 'delivery' );
		$response->addSnippet( 'payment' );
		$response->addSnippet( 'customer' );

		$response->response();

	}
	
	public function whisper_point_Action() : void
	{
		$GET = Http_Request::GET();
		
		$q = $GET->getString('q');
		if(!$q) {
			die();
		}
		
		$cash_desk = $this->cash_desk;
		
		$methods = $cash_desk->getAvailableDeliveryMethods();
		
		$only_methods = explode(',', trim($GET->getString('methods')));
		$only_method_ids = array_intersect($only_methods, array_keys($methods));
		$only_methods = [];
		
		foreach($only_method_ids as $method_id) {
			$method = $methods[$method_id]??null;
			
			if($method) {
				$only_methods[] = $method;
			}
		}
		
		
		
		$result = Carrier_DeliveryPoint::search( $q, $only_methods );
		
		if(!$result):
			//echo $this->view->render('delivery/personal_takeover/search/not_found');
			Application::end();
		endif;
		
		$c = 0;
		foreach( $result as $item ):
			$c++;
			
			$this->view->setVar('c', $c);
			$this->view->setVar('point', $item['point']);
			$this->view->setVar('method', $item['method'] );
			
			echo $this->view->render('delivery/method/personal_takeover/search/point');
		endforeach;
		Application::end();
		
	}
}
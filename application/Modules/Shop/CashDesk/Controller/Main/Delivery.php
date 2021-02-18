<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetShopModule\Shop\CashDesk;

use Jet\AJAX;
use Jet\Application;
use Jet\Http_Request;
use JetShop\CashDesk;
use JetShop\Delivery_PersonalTakeover_Place;
use JetShop\Shops;

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
		$this->router->addAction('personal_takeover_show_place')->setResolver(function() use ($action) {
			return $action=='personal_takeover_show_place';
		});

		$this->router->addAction('personal_takeover_select_place')->setResolver(function() use ($action) {
			return $action=='personal_takeover_select_place';
		});

		$this->router->addAction('continue_to_payment')->setResolver(function() use ($action) {
			return $action=='continue_to_payment';
		});

		$this->router->addAction('back_to_delivery')->setResolver(function() use ($action) {
			return $action=='back_to_delivery';
		});

	}


	public function personal_takeover_get_map_data_Action() : void
	{
		$only_method_codes = [];

		$GET = Http_Request::GET();

		if($GET->exists('only_methods')) {
			$only_method_codes = explode(',', $GET->getString('only_methods'));
		}

		AJAX::response( Delivery_PersonalTakeover_Place::getMapData( only_method_codes: $only_method_codes ) );
	}

	public function personal_takeover_show_place_Action(): void
	{
		$GET = Http_Request::GET();

		$id = $GET->getString('id');
		if(
			!$id ||
			!str_contains($id, ':')
		) {
			Application::end();
		}

		[$method_code, $place_code] = explode(':', $id);

		$place = Delivery_PersonalTakeover_Place::getPlace(
			Shops::getCurrentCode(),
			$method_code,
			$place_code
		);

		if(!$place) {
			Application::end();
		}

		$this->view->setVar('place', $place);

		echo $this->view->render('delivery/method/personal_takeover/place_detail');

		Application::end();
	}

	public function personal_takeover_select_place_Action() : void
	{
		/**
		 * @var Controller_Main $this
		 */
		$response = new Controller_Main_Response( $this );
		$cash_desk = CashDesk::get();
		$GET = Http_Request::GET();

		if(!$cash_desk->selectPersonalTakeoverPlace( $GET->getString('method'), $GET->getString('place') )) {
			$response->error();
		}

		$response->addSnippet( 'overview' );
		$response->addSnippet( 'delivery' );

		$response->response();

	}

	public function select_delivery_Action() : void
	{
		/**
		 * @var Controller_Main $this
		 */

		$response = new Controller_Main_Response( $this );
		$cash_desk = CashDesk::get();
		$GET = Http_Request::GET();

		if(!$cash_desk->selectDeliveryMethod( $GET->getString('method') )) {
			$response->error();
		}

		$response->addSnippet( 'overview' );
		$response->addSnippet( 'delivery' );

		$response->response();
	}

	public function continue_to_payment_Action()
	{
		/**
		 * @var Controller_Main $this
		 */

		$response = new Controller_Main_Response( $this );
		$cash_desk = CashDesk::get();

		$cash_desk->setCurrentStep( CashDesk::STEP_PAYMENT );

		$response->addSnippet( 'overview' );
		$response->addSnippet( 'delivery' );
		$response->addSnippet( 'payment' );


		$response->response();

	}

	public function back_to_delivery_Action()
	{
		/**
		 * @var Controller_Main $this
		 */

		$response = new Controller_Main_Response( $this );
		$cash_desk = CashDesk::get();

		$cash_desk->setCurrentStep( CashDesk::STEP_DELIVERY );

		$response->addSnippet( 'overview' );
		$response->addSnippet( 'delivery' );
		$response->addSnippet( 'payment' );
		$response->addSnippet( 'customer' );

		$response->response();

	}

}
<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Shop\CashDesk;

use Jet\Http_Request;

trait Controller_Main_Payment {

	public function getControllerRouter_Payment() : void
	{

		$GET = Http_Request::GET();
		$action = $GET->getString('action');


		$this->router->addAction('select_payment')->setResolver(function() use ($action) {
			return $action=='select_payment';
		});

		$this->router->addAction('select_payment_option')->setResolver(function() use ($action) {
			return $action=='select_payment_option';
		});

		$this->router->addAction('continue_to_customer')->setResolver(function() use ($action) {
			return $action=='continue_to_customer';
		});

		$this->router->addAction('back_to_payment')->setResolver(function() use ($action) {
			return $action=='back_to_payment';
		});


	}
	
	public function select_payment_Action() : void
	{

		$response = new Controller_Main_Response( $this );
		$cash_desk = CashDesk::get();
		$GET = Http_Request::GET();

		if(!$cash_desk->selectPaymentMethod( $GET->getString('method') )) {
			$response->error();
		}

		$response->addSnippet( 'overview' );
		$response->addSnippet( 'payment' );
		$response->addSnippet( 'customer' );
		
		if($cash_desk->isReady()) {
			$cash_desk->setCurrentStep( CashDesk::STEP_CONFIRM );
		}

		$response->response();
	}

	public function select_payment_option_Action() : void
	{

		$response = new Controller_Main_Response( $this );
		$cash_desk = CashDesk::get();
		$GET = Http_Request::GET();

		if(!$cash_desk->selectPaymentMethodOption( $GET->getString('option') )) {
			$response->error();
		}

		$response->addSnippet( 'overview' );
		$response->addSnippet( 'payment' );
		$response->addSnippet( 'customer' );

		$response->response();
	}


	public function continue_to_customer_Action() : void
	{

		$response = new Controller_Main_Response( $this );
		$cash_desk = CashDesk::get();

		$cash_desk->setCurrentStep( CashDesk::STEP_CUSTOMER );

		$response->addSnippet( 'overview' );
		$response->addSnippet( 'payment' );
		$response->addSnippet( 'customer' );


		$response->response();

	}

	public function back_to_payment_Action() : void
	{

		$response = new Controller_Main_Response( $this );
		$cash_desk = CashDesk::get();

		$cash_desk->setCurrentStep( CashDesk::STEP_PAYMENT );

		$response->addSnippet( 'overview' );
		$response->addSnippet( 'payment' );
		$response->addSnippet( 'customer' );

		$response->response();

	}
}
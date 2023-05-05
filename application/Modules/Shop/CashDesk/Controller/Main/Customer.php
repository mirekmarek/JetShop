<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Shop\CashDesk;

use Jet\Auth;
use Jet\Data_Array;
use Jet\Http_Request;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\CashDesk;
use JetApplication\Customer;

trait Controller_Main_Customer {

	public function getControllerRouter_Customer() : void
	{

		$GET = Http_Request::GET();
		$action = $GET->getString('action');


		$this->router->addAction('customer_set_email')->setResolver(function() use ($action) {
			return $action=='customer_set_email';
		});

		$this->router->addAction('customer_back_to_set_email')->setResolver(function() use ($action) {
			return $action=='customer_back_to_set_email';
		});

		$this->router->addAction('customer_set_password')->setResolver(function() use ($action) {
			return $action=='customer_set_password';
		});

		$this->router->addAction('customer_registration_back')->setResolver(function() use ($action) {
			return $action=='customer_registration_back';
		});

		$this->router->addAction('customer_registration_do_not_register')->setResolver(function() use ($action) {
			return $action=='customer_registration_do_not_register';
		});

		$this->router->addAction('customer_set_is_person')->setResolver(function() use ($action) {
			return $action=='customer_set_is_person';
		});

		$this->router->addAction('customer_set_is_company')->setResolver(function() use ($action) {
			return $action=='customer_set_is_company';
		});

		$this->router->addAction('customer_billing_address_catch_field')->setResolver(function() use ($action) {
			return $action=='customer_billing_address_catch_field';
		});

		$this->router->addAction('customer_billing_address_send')->setResolver(function() use ($action) {
			return $action=='customer_billing_address_send';
		});

		$this->router->addAction('customer_back_to_billing_address')->setResolver(function() use ($action) {
			return $action=='customer_back_to_billing_address';
		});

		$this->router->addAction('customer_delivery_address_set_the_same')->setResolver(function() use ($action) {
			return $action=='customer_delivery_address_set_the_same';
		});

		$this->router->addAction('customer_delivery_address_set_different')->setResolver(function() use ($action) {
			return $action=='customer_delivery_address_set_different';
		});

		$this->router->addAction('customer_back_to_delivery_address')->setResolver(function() use ($action) {
			return $action=='customer_back_to_delivery_address';
		});


		$this->router->addAction('customer_delivery_address_catch_field')->setResolver(function() use ($action) {
			return $action=='customer_delivery_address_catch_field';
		});

		$this->router->addAction('customer_delivery_address_send')->setResolver(function() use ($action) {
			return $action=='customer_delivery_address_send';
		});

		$this->router->addAction('customer_login')->setResolver(function() use ($action) {
			return $action=='customer_login';
		});

		$this->router->addAction('customer_billing_address_select')->setResolver(function() use ($action) {
			return $action=='customer_billing_address_select';
		});

		$this->router->addAction('customer_delivery_address_select')->setResolver(function() use ($action) {
			return $action=='customer_delivery_address_select';
		});


	}

	public function customer_set_email_Action() : void
	{

		$response = new Controller_Main_Response( $this );
		$cash_desk = CashDesk::get();

		$form = $cash_desk->getSetEMailForm();

		if($cash_desk->catchSetEMailForm()) {
			$cash_desk->setEmailAddress( $form->field('email')->getValue() );
		} else {
			$response->error();
		}

		$response->addSnippet('customer');

		$response->response();
	}

	public function customer_back_to_set_email_Action() : void
	{

		$response = new Controller_Main_Response( $this );
		$cash_desk = CashDesk::get();

		$cash_desk->setEmailHasBeenSet( false );

		$response->addSnippet('customer');

		$response->response();
	}


	public function customer_set_password_Action()
	{

		$response = new Controller_Main_Response( $this );
		$cash_desk = CashDesk::get();

		if(!$cash_desk->catchSetPasswordForm()) {
			$response->error();
		}

		$response->addSnippet('customer');

		$response->response();
	}

	public function customer_registration_back_Action()
	{

		$response = new Controller_Main_Response( $this );
		$cash_desk = CashDesk::get();

		$cash_desk->setCustomerRegisterOrNotBeenSet(false);
		$response->addSnippet('customer');

		$response->response();
	}

	public function customer_registration_do_not_register_Action()
	{

		$response = new Controller_Main_Response( $this );
		$cash_desk = CashDesk::get();

		$cash_desk->setNoRegistration(true);
		$cash_desk->setCustomerRegisterOrNotBeenSet(true);

		$response->addSnippet('customer');

		$response->response();

	}


	public function customer_set_is_person_Action()
	{

		$response = new Controller_Main_Response( $this );
		$cash_desk = CashDesk::get();

		$cash_desk->setIsCompanyOrder(false);
		$response->addSnippet('customer');
		$response->response();
	}

	public function customer_set_is_company_Action()
	{

		$response = new Controller_Main_Response( $this );
		$cash_desk = CashDesk::get();

		$cash_desk->setIsCompanyOrder(true);
		$response->addSnippet('customer');
		$response->response();
	}

	public function customer_billing_address_catch_field_Action()
	{

		$response = new Controller_Main_Response( $this );
		$cash_desk = CashDesk::get();
		$GET = Http_Request::GET();

		$field_name = $GET->getString('field');
		$value = $GET->getString('value');


		$form = $cash_desk->getBillingAddressForm();

		$field = $form->getField($field_name);

		$field->catchInput( new Data_Array([$field_name=>$value]) );

		if( !$field->validate() ) {
			$response->error();
		}

		$field->catchFieldValue();


		if($field->getValue() && !$field->getLastErrorCode()) {
			$field->input()->addCustomCssClass('is-valid');
		}

		$response->addSnippet(
			'field_'.$field->getId(), $field
		);

		$response->response();
	}

	public function customer_billing_address_send_Action()
	{
		$response = new Controller_Main_Response( $this );
		$cash_desk = CashDesk::get();


		$form = $cash_desk->getBillingAddressForm();

		if( $form->catch() ) {
			$cash_desk->setBillingAddressHasBeenSet(true);
			
			if($cash_desk->isDeliveryAddressDisabled()) {
				$cash_desk->setCurrentStep( CashDesk::STEP_CONFIRM );
				
			} else {
				$cash_desk->setDifferentDeliveryAddressHasBeenSet( false );
				$cash_desk->setDeliveryAddressHasBeenSet( false );
				
				$response->addSnippet(
					'cash_desk_delivery_address',
					'customer/delivery_address'
				);
				
			}
		} else {
			$response->error();
		}

		$response->addSnippet( 'cash_desk_billing_address', 'customer/billing_address' );


		$response->response();
	}

	public function customer_back_to_billing_address_Action()
	{
		$response = new Controller_Main_Response( $this );
		$cash_desk = CashDesk::get();

		$cash_desk->setBillingAddressHasBeenSet(false);
		$cash_desk->setDifferentDeliveryAddressHasBeenSet(false);
		$cash_desk->setDeliveryAddressHasBeenSet(false);

		$response->addSnippet(
			'cash_desk_billing_address', 'customer/billing_address'
		);

		$response->response();
	}


	public function customer_delivery_address_set_the_same_Action()
	{
		$response = new Controller_Main_Response( $this );
		$cash_desk = CashDesk::get();

		$cash_desk->setDifferentDeliveryAddressHasBeenSet(true);
		$cash_desk->setDeliveryAddressHasBeenSet(true);

		$cash_desk->setHasDifferentDeliveryAddress(false);

		$cash_desk->setCurrentStep( CashDesk::STEP_CONFIRM );

		$response->addSnippet(
			'cash_desk_delivery_address', 'customer/delivery_address'
		);

		$response->response();
	}

	public function customer_delivery_address_set_different_Action()
	{
		$response = new Controller_Main_Response( $this );
		$cash_desk = CashDesk::get();

		$cash_desk->setDifferentDeliveryAddressHasBeenSet(true);
		$cash_desk->setHasDifferentDeliveryAddress(true);

		$response->addSnippet(
			'cash_desk_delivery_address', 'customer/delivery_address'
		);

		$response->response();
	}

	public function customer_back_to_delivery_address_Action()
	{
		$response = new Controller_Main_Response( $this );
		$cash_desk = CashDesk::get();

		$cash_desk->setDeliveryAddressHasBeenSet(false);
		$cash_desk->setDifferentDeliveryAddressHasBeenSet(false);

		$response->addSnippet(
			'cash_desk_delivery_address', 'customer/delivery_address'
		);

		$response->response();

	}


	public function customer_delivery_address_catch_field_Action()
	{

		$response = new Controller_Main_Response( $this );
		$cash_desk = CashDesk::get();
		$GET = Http_Request::GET();

		$field_name = $GET->getString('field');
		$value = $GET->getString('value');


		$form = $cash_desk->getDeliveryAddressForm();

		$field = $form->getField($field_name);

		$field->catchInput( new Data_Array([$field_name=>$value]) );

		if( !$field->validate() ) {
			$response->error();
		}

		$field->catchFieldValue();


		if($field->getValue() && !$field->getLastErrorCode()) {
			$field->input()->addCustomCssClass('is-valid');
		}

		$response->addSnippet(
			'field_'.$field->getId(), $field
		);

		$response->response();
	}

	public function customer_delivery_address_send_Action()
	{
		$response = new Controller_Main_Response( $this );
		$cash_desk = CashDesk::get();


		$form = $cash_desk->getDeliveryAddressForm();

		if( $form->catch() ) {
			$cash_desk->setDeliveryAddressHasBeenSet(true);
			$cash_desk->setCurrentStep( CashDesk::STEP_CONFIRM );
		} else {
			$response->error();
		}

		$response->addSnippet( 'cash_desk_delivery_address', 'customer/delivery_address' );

		$response->response();
	}

	public function customer_login_Action()
	{
		$response = new Controller_Main_Response( $this );
		$cash_desk = CashDesk::get();


		$form = $cash_desk->getLoginForm();

		if( !$form->catch() ) {
			$response->error();
		} else {
			$password = $form->field('password')->getValue();

			if(!Auth::login( $cash_desk->getEmailAddress(), $password )) {
				$form->setCommonMessage( UI_messages::createDanger(Tr::_('Incorrect password')) );
				$response->error();
			} /** @noinspection PhpStatementHasEmptyBodyInspection */ else {
				//TODO:
			}
		}

		$response->addSnippet('customer');

		$response->response();

	}

	public function customer_billing_address_select_Action()
	{
		$response = new Controller_Main_Response( $this );
		$cash_desk = CashDesk::get();
		$GET = Http_Request::GET();

		$customer = Customer::getCurrentCustomer();

		$address_id = $GET->getInt('id');
		$address = $customer->getAddress( $address_id );

		if($address) {
			$cash_desk->setBillingAddress( $address );
		}

		$response->addSnippet('customer');

		$response->response();

	}

	public function customer_delivery_address_select_Action()
	{
		$response = new Controller_Main_Response( $this );
		$cash_desk = CashDesk::get();
		$GET = Http_Request::GET();

		$customer = Customer::getCurrentCustomer();

		$address_id = $GET->getInt('id');
		$address = $customer->getAddress( $address_id );

		if($address) {
			$cash_desk->setDeliveryAddress( $address );
		}

		$response->addSnippet('customer');

		$response->response();

	}

}


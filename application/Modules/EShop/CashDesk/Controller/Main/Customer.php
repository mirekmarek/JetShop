<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\CashDesk;


use Jet\Auth;
use Jet\Data_Array;
use Jet\Http_Request;
use Jet\Tr;
use Jet\UI_messages;
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
		
		$this->router->addAction('customer_set_phone')->setResolver(function() use ($action) {
			return $action=='customer_set_phone';
		});
		
		$this->router->addAction('customer_back_to_set_phone')->setResolver(function() use ($action) {
			return $action=='customer_back_to_set_phone';
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
		
		$this->router->addAction('customer_delivery_address_set_the_same_confirm')->setResolver(function() use ($action) {
			return $action=='customer_delivery_address_set_the_same_confirm';
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
		
		$this->router->addAction('whisper_address')->setResolver(function() use ($action) {
			return $action=='whisper_address';
		});
		
	}

	public function customer_set_email_Action() : void
	{

		$response = new Controller_Main_Response( $this );
		$cash_desk = $this->cash_desk;

		$form = $cash_desk->getSetEMailForm();

		if($cash_desk->catchSetEMailForm()) {
			$cash_desk->setEmailAddress( $form->field('email')->getValue() );
			$cash_desk->setPhoneHasBeenSet( false );
			$cash_desk->setCustomerRegisterOrNotBeenSet( false );
		} else {
			$response->error();
		}

		$response->addSnippet('customer');

		$response->response();
	}

	public function customer_back_to_set_email_Action() : void
	{

		$response = new Controller_Main_Response( $this );
		$cash_desk = $this->cash_desk;

		$cash_desk->setEmailHasBeenSet( false );

		$response->addSnippet('customer');

		$response->response();
	}
	
	
	public function customer_set_phone_Action() : void
	{
		
		$response = new Controller_Main_Response( $this );
		$cash_desk = $this->cash_desk;
		
		$form = $cash_desk->getPhoneForm();
		
		if($form->catch()) {
			$cash_desk->setPhone( $form->field('phone')->getValue() );
		} else {
			$response->error();
		}
		
		$response->addSnippet('customer');
		
		$response->response();
	}
	
	public function customer_back_to_set_phone_Action() : void
	{
		
		$response = new Controller_Main_Response( $this );
		$cash_desk = $this->cash_desk;
		
		$cash_desk->setPhoneHasBeenSet( false );
		
		$response->addSnippet('customer');
		
		$response->response();
	}
	

	public function customer_set_password_Action() : void
	{

		$response = new Controller_Main_Response( $this );
		$cash_desk = $this->cash_desk;

		if(!$cash_desk->catchSetPasswordForm()) {
			$response->error();
		}
		
		$this->cash_desk->resetDiscounts();

		$response->addSnippet('customer');
		$response->addSnippet('delivery');
		$response->addSnippet('overview');

		$response->response();
	}

	public function customer_registration_back_Action() : void
	{

		$response = new Controller_Main_Response( $this );
		$cash_desk = $this->cash_desk;

		$cash_desk->setCustomerRegisterOrNotBeenSet(false);
		$response->addSnippet('customer');

		$response->response();
	}

	public function customer_registration_do_not_register_Action() : void
	{

		$response = new Controller_Main_Response( $this );
		$cash_desk = $this->cash_desk;

		$cash_desk->setNoRegistration(true);
		$cash_desk->setCustomerRegisterOrNotBeenSet(true);
		
		$this->cash_desk->resetDiscounts();
		
		
		$response->addSnippet('customer');
		$response->addSnippet('delivery');
		$response->addSnippet('overview');

		$response->response();

	}


	public function customer_set_is_person_Action() : void
	{

		$response = new Controller_Main_Response( $this );
		$cash_desk = $this->cash_desk;

		$cash_desk->setIsCompanyOrder(false);
		$response->addSnippet('customer');
		$response->response();
	}

	public function customer_set_is_company_Action() : void
	{

		$response = new Controller_Main_Response( $this );
		$cash_desk = $this->cash_desk;

		$cash_desk->setIsCompanyOrder(true);
		$response->addSnippet('customer');
		$response->response();
	}

	public function customer_billing_address_catch_field_Action() : void
	{

		$response = new Controller_Main_Response( $this );
		$cash_desk = $this->cash_desk;
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

	public function customer_billing_address_send_Action() : void
	{
		$response = new Controller_Main_Response( $this );
		$cash_desk = $this->cash_desk;

		
		$form = $cash_desk->getBillingAddressForm();
		
		$form->validate();
		
		if( !$form->getValidationErrors() ) {
			$cash_desk->setBillingAddressHasBeenSet(true);
			
			if($cash_desk->isDeliveryAddressDisabled()) {
				$cash_desk->setCurrentStep( CashDesk::STEP_CONFIRM );
				
			} else {
				$cash_desk->setDifferentDeliveryAddressHasBeenSet( false );
				$cash_desk->setDeliveryAddressHasBeenSet( false );
				
			}
		} else {
			$response->error();
		}
		
		$response->addSnippet('customer');


		$response->response();
	}

	public function customer_back_to_billing_address_Action() : void
	{
		$response = new Controller_Main_Response( $this );
		$cash_desk = $this->cash_desk;

		$cash_desk->setBillingAddressHasBeenSet(false);
		$cash_desk->setDifferentDeliveryAddressHasBeenSet(false);
		$cash_desk->setDeliveryAddressHasBeenSet(false);

		$response->addSnippet( 'customer' );

		$response->response();
	}


	public function customer_delivery_address_set_the_same_Action() : void
	{
		$response = new Controller_Main_Response( $this );
		$cash_desk = $this->cash_desk;

		$cash_desk->setDifferentDeliveryAddressHasBeenSet(true);

		$cash_desk->setHasDifferentDeliveryAddress(false);
		

		$response->addSnippet( 'customer' );

		$response->response();
	}
	
	public function customer_delivery_address_set_the_same_confirm_Action() : void
	{
		$response = new Controller_Main_Response( $this );
		$cash_desk = $this->cash_desk;
		
		$cash_desk->setDifferentDeliveryAddressHasBeenSet(true);
		
		$cash_desk->setHasDifferentDeliveryAddress(false);
		
		$cash_desk->setDeliveryAddressHasBeenSet(true);
		$cash_desk->setCurrentStep( CashDesk::STEP_CONFIRM );
		
		$response->addSnippet( 'customer' );
		
		$response->response();
	}
	

	public function customer_delivery_address_set_different_Action() : void
	{
		$response = new Controller_Main_Response( $this );
		$cash_desk = $this->cash_desk;

		$cash_desk->setDifferentDeliveryAddressHasBeenSet(true);
		$cash_desk->setHasDifferentDeliveryAddress(true);

		$response->addSnippet( 'customer' );

		$response->response();
	}

	public function customer_back_to_delivery_address_Action() : void
	{
		$response = new Controller_Main_Response( $this );
		$cash_desk = $this->cash_desk;

		$cash_desk->setDeliveryAddressHasBeenSet(false);
		$cash_desk->setDifferentDeliveryAddressHasBeenSet(false);

		$response->addSnippet( 'customer' );

		$response->response();

	}


	public function customer_delivery_address_catch_field_Action() : void
	{

		$response = new Controller_Main_Response( $this );
		$cash_desk = $this->cash_desk;
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

	public function customer_delivery_address_send_Action() : void
	{
		$response = new Controller_Main_Response( $this );
		$cash_desk = $this->cash_desk;


		$form = $cash_desk->getDeliveryAddressForm();

		if( $form->catch() ) {
			$cash_desk->setDeliveryAddressHasBeenSet(true);
			$cash_desk->setCurrentStep( CashDesk::STEP_CONFIRM );
		} else {
			$response->error();
		}

		$response->addSnippet( 'customer' );

		$response->response();
	}

	public function customer_login_Action() : void
	{
		$response = new Controller_Main_Response( $this );
		$cash_desk = $this->cash_desk;


		$form = $cash_desk->getLoginForm();

		if( !$form->catch() ) {
			$response->error();
		} else {
			$password = $form->field('password')->getValue();

			if(!Auth::login( $cash_desk->getEmailAddress(), $password )) {
				$form->setCommonMessage( UI_messages::createDanger(Tr::_('Incorrect password')) );
				$response->error();
			}
		}

		$response->addSnippet('customer');

		$response->response();

	}

	public function customer_billing_address_select_Action() : void
	{
		$response = new Controller_Main_Response( $this );
		$cash_desk = $this->cash_desk;
		$GET = Http_Request::GET();

		$customer = Customer::getCurrentCustomer();

		$address_id = $GET->getInt('id');
		$address = $customer->getAddress( $address_id );

		if($address) {
			$cash_desk->setBillingAddress( clone $address );
		}

		$response->addSnippet('customer');

		$response->response();

	}

	public function customer_delivery_address_select_Action() : void
	{
		$response = new Controller_Main_Response( $this );
		$cash_desk = $this->cash_desk;
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

	public function whisper_address_Action() : void
	{
	}
}


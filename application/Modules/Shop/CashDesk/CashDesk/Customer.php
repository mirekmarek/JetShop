<?php
namespace JetApplicationModule\Shop\CashDesk;

use Jet\Auth;
use Jet\Data_DateTime;
use Jet\Form;
use Jet\Form_Field;
use Jet\Form_Field_Email;
use Jet\Form_Field_Input;
use Jet\Form_Field_Password;
use Jet\Http_Request;
use Jet\Session;

use JetApplication\Customer;
use JetApplication\Customer_Address;


trait CashDesk_Customer
{
	protected ?Form $mail_form = null;

	protected ?Form $set_password_form = null;

	protected ?Form $login_form = null;
	
	protected ?Form $phone_form = null;
	
	protected ?Form $billing_address_form = null;

	protected ?Form $delivery_address_form = null;
	
	
	public function checkCurrentCustomer() : void
	{
		$session = $this->getSession();
		
		$current_customer_id = Customer::getCurrentCustomer()?->getId()?:0;
		
		$session_customer_id = $session->getValue('customer_id', 0 );
		
		if($current_customer_id==$session_customer_id) {
			return;
		}

		$session->setValue('customer_id', $current_customer_id);
		
		if(!$current_customer_id) {
			$this->onCustomerLogout();
		} else {
			$this->onCustomerLogin();
		}
	}

	
	public function onCustomerLogin() : void
	{
		$session = $this->getSession();
		
		$session->unsetValue('billing_address');
		$session->unsetValue('delivery_address');
		
		if($this->getCurrentStep()==CashDesk::STEP_CONFIRM) {
			$this->setCurrentStep(CashDesk::STEP_CUSTOMER);
		}
		
		$customer = Customer::getCurrentCustomer();
		
		$this->setBillingAddressHasBeenSet(false);
		$this->setDeliveryAddressHasBeenSet(false);
		
		$this->setEmailAddress($customer->getEmail());
		$this->setPhone($customer->getPhoneNumber());
		

		$default_address = $customer->getDefaultAddress();
		
		if($default_address) {
			$this->setBillingAddress( clone $default_address );
		}
		
		foreach($this->getAgreeFlags() as $flag) {
			$flag->onCustomerLogin( $this );
		}
		
	}
	
	public function onCustomerLogout() : void
	{
		$session = $this->getSession();
		
		$session->unsetValue('billing_address');
		$session->unsetValue('delivery_address');
		
		if($this->getCurrentStep()==CashDesk::STEP_CONFIRM) {
			$this->setCurrentStep(CashDesk::STEP_CUSTOMER);
		}
		
		$this->setEmailAddress('');
		$this->setPhone('');
		$this->setBillingAddressHasBeenSet(false);
		$this->setDeliveryAddressHasBeenSet(false);
		$this->setEmailHasBeenSet(false);
		$this->setDifferentDeliveryAddressHasBeenSet(false);
		
		foreach($this->getAgreeFlags() as $flag) {
			$flag->onCustomerLogout( $this );
		}
		
	}
	

	public function getEmailAddress() : string
	{
		$session = $this->getSession();

		return $session->getValue('email_address', '');
	}

	public function setEmailAddress( string $email ) : void
	{
		$session = $this->getSession();

		$session->setValue('email_address', $email);
		$this->setEmailHasBeenSet(true);
	}


	public function getSetEMailForm() : Form
	{
		if(!$this->mail_form) {
			$email = new Form_Field_Email('email', 'E-mail:');
			$email->setIsRequired(true);
			$email->setDefaultValue( $this->getEmailAddress() );
			$email->setErrorMessages([
				Form_Field_Email::ERROR_CODE_EMPTY => 'Please enter your e-mail address',
				Form_Field_Email::ERROR_CODE_INVALID_FORMAT => 'Invalid e-mail address format. Please check it.'
			]);

			$email->setFieldValueCatcher(function($value) {
				$this->setEmailHasBeenSet($value);
			});

			$this->mail_form = new Form('cash_desk_set_email_form', [$email]);
			$this->mail_form->setAction('?action=customer_set_email');
		}

		return $this->mail_form;
	}

	public function catchSetEMailForm() : bool
	{
		return $this->getSetEMailForm()->catch();
	}


	public function setPassword( string $password ) : void
	{
		$session = $this->getSession();

		$this->setNoRegistration( false );
		$this->setCustomerRegisterOrNotBeenSet( true );

		$password = (new Customer())->encryptPassword( $password );

		$session->setValue('password', $password);
	}

	public function getPassword() : string
	{
		$session = $this->getSession();

		$this->setNoRegistration( false );

		return $session->getValue('password', '');
	}

	public function setNoRegistration( bool $state ) : void
	{
		$session = $this->getSession();

		$session->setValue('no_registration', $state);
	}

	public function getNoRegistration() : bool
	{
		$session = $this->getSession();

		return (bool)$session->getValue('no_registration', false);
	}

	public function getSetPasswordForm() : Form
	{
		if(!$this->set_password_form) {
			$password = new Form_Field_Password('password', 'Password:');
			$password->setIsRequired(true);
			$password->setErrorMessages([
				Form_Field::ERROR_CODE_EMPTY => 'Please enter password',
			]);
			
			$password_confirm = $password->generateCheckField(
				'password_confirm',
				'Confirm password:',
				'Please confirm password',
				'Password confirmation does not match'
			);
			
			

			$password->setFieldValueCatcher(function($value) {
				$this->setPassword( $value );
			});

			$this->set_password_form = new Form('registration_set_password_form', [$password, $password_confirm]);
			$this->set_password_form->setAction('?action=customer_set_password');
		}

		return $this->set_password_form;
	}

	public function catchSetPasswordForm() : bool
	{
		return $this->getSetPasswordForm()->catch();
	}


	public function getBillingAddress() : Customer_Address
	{
		/**
		 * @var Session $session
		 * @var Customer_Address $billing_address
		 */
		$session = $this->getSession();

		$billing_address = $session->getValue('billing_address');

		if(
			!$billing_address ||
			!($billing_address instanceof Customer_Address)
		) {
			$billing_address = new Customer_Address();
			
			$billing_address->setAddressCountry( $this->shop->getLocale()->getRegion() );

			$session->setValue('billing_address', $billing_address);
		}

		return $billing_address;
	}

	public function setBillingAddress( Customer_Address $address ) : void
	{
		/**
		 * @var Session $session
		 * @var Customer_Address $billing_address
		 */
		$session = $this->getSession();

		$session->setValue('billing_address', $address);
	}
	
	public function getPhoneForm() : Form
	{
		
		if(!$this->phone_form) {
			
			
			$shop = $this->getShop();
			$phone = new Form_Field_Input('phone', 'Phone number:');
			$phone->setDefaultValue( $this->getPhone() );
			$phone->setIsRequired( true );
			$phone->setErrorMessages([
				Form_Field::ERROR_CODE_EMPTY => 'Please enter your phone number',
				Form_Field::ERROR_CODE_INVALID_FORMAT => 'Please enter your phone number'
			]);
			$phone->setFieldValueCatcher(function($value) {
				$this->setPhone($value);
			});
			
			$phone->setValidator( function( Form_Field_Input $field ) {
				return static::phoneValidator( $field, $this->config );
			} );
			
			
			$this->phone_form = new Form('cash_desk_set_phone_form', [$phone]);
			
			$this->phone_form->setAction('?action=customer_set_phone');
			
			$this->phone_form->renderer()->addJsAction('onsubmit', "CashDesk.customer.phone.set();return false;");
		}
		
		return $this->phone_form;
	}


	public function getBillingAddressForm() : Form
	{
	
		if(!$this->billing_address_form) {
			$this->billing_address_form = $this->getBillingAddress()->createForm('cash_desk_customer_billing_address_form');
			
			if($this->isDeliveryAddressDisabled()) {
				$this->billing_address_form->field('address_town')->setIsRequired(false);
				$this->billing_address_form->field('address_zip')->setIsRequired(false);
				$this->billing_address_form->field('address_street_no')->setIsRequired(false);
			} else {
				$this->billing_address_form->field('address_town')->setIsRequired(true);
				$this->billing_address_form->field('address_town')->setErrorMessages([
					Form_Field::ERROR_CODE_EMPTY => "Please enter town"
				]);

				$this->billing_address_form->field('address_zip')->setIsRequired(true);
				$this->billing_address_form->field('address_zip')->setErrorMessages([
					Form_Field::ERROR_CODE_EMPTY => "Please enter ZIP"
				]);

				$this->billing_address_form->field('address_street_no')->setIsRequired(true);
				$this->billing_address_form->field('address_street_no')->setErrorMessages([
					Form_Field::ERROR_CODE_EMPTY => "Please enter street and number"
				]);
			}


			if($this->isCompanyOrder()) {
				$this->billing_address_form->field('first_name')->setIsRequired(false);
				$this->billing_address_form->field('surname')->setIsRequired(false);

				$this->billing_address_form->field('company_name')->setIsRequired(true);
				$this->billing_address_form->field('company_name')->setErrorMessages([
					Form_Field::ERROR_CODE_EMPTY => 'Please enter company name'
				]);

				$this->billing_address_form->field('company_id')->setIsRequired(true);
				$this->billing_address_form->field('company_id')->setErrorMessages([
					Form_Field::ERROR_CODE_EMPTY => 'Please enter company ID'
				]);
			} else {
				$this->billing_address_form->field('first_name')->setIsRequired(true);
				$this->billing_address_form->field('first_name')->setErrorMessages([
					Form_Field::ERROR_CODE_EMPTY => 'Please enter first name'
				]);
				$this->billing_address_form->field('surname')->setIsRequired(true);
				$this->billing_address_form->field('surname')->setErrorMessages([
					Form_Field::ERROR_CODE_EMPTY => 'Please enter surname'
				]);

			}
			
			$this->billing_address_form->setAction('?action=customer_billing_address_send');
			
			$this->billing_address_form->renderer()->addJsAction('onsubmit', "CashDesk.customer.billingAddress.confirm();return false;");
			
			foreach($this->billing_address_form->getFields() as $field) {
				$field->input()->addJsAction('onblur', "CashDesk.customer.billingAddress.sendField(this);");
			}
			
		}

		return $this->billing_address_form;
	}

	public function isCompanyOrder() : bool
	{
		$session = $this->getSession();

		return (bool)$session->getValue('is_company_order', false);
	}

	public function setIsCompanyOrder( bool $state ) : void
	{
		$session = $this->getSession();

		$session->setValue('is_company_order', $state);
	}

	public function getPhone() : string
	{
		$session = $this->getSession();

		return $session->getValue('phone', '');
	}
	
	public function getPhoneWithPrefix() : string
	{
		return $this->config->getPhonePrefix().$this->getPhone();
	}

	public function setPhone( string $phone ) : void
	{
		$session = $this->getSession();

		$session->setValue('phone', $phone);
		$this->setPhoneHasBeenSet( true );
	}


	public function hasDifferentDeliveryAddress() : bool
	{
		$session = $this->getSession();

		return $session->getValue('different_delivery_address',false);
	}

	public function setHasDifferentDeliveryAddress( bool $state ) : void
	{
		$session = $this->getSession();

		$session->setValue('different_delivery_address', $state);
	}




	public function getDeliveryAddress() : ?Customer_Address
	{
		/**
		 * @var Session $session
		 * @var Customer_Address $delivery_address
		 */

		if($this->getSelectedDeliveryMethod()->isEDelivery()) {
			return null;
		}

		if($this->getSelectedDeliveryMethod()->isPersonalTakeover()) {
			$place = $this->getSelectedPersonalTakeoverDeliveryPoint();
			$billing_address = $this->getBillingAddress();

			$delivery_address = new Customer_Address();
			$delivery_address->setFirstName( $billing_address->getFirstName() );
			$delivery_address->setSurname( $billing_address->getSurname() );
			$delivery_address->setCompanyName( $place->getName() );
			$delivery_address->setAddressStreetNo( $place->getStreet() );
			$delivery_address->setAddressTown( $place->getTown() );
			$delivery_address->setAddressZip( $place->getZip() );
			$delivery_address->setAddressCountry( $place->getCountry() );

			return $delivery_address;
		}

		if(!$this->hasDifferentDeliveryAddress()) {
			return $this->getBillingAddress();
		}


		$session = $this->getSession();

		$delivery_address = $session->getValue('delivery_address');

		if(
			!$delivery_address ||
			!($delivery_address instanceof Customer_Address)
		) {
			$delivery_address = new Customer_Address();
			$delivery_address->setAddressCountry( $this->shop->getLocale()->getRegion() );

			$session->setValue('delivery_address', $delivery_address);
		}

		return $delivery_address;
	}

	public function setDeliveryAddress( Customer_Address $address ) : void
	{
		/**
		 * @var Session $session
		 * @var Customer_Address $billing_address
		 */
		$session = $this->getSession();

		if(
			!$this->isDeliveryAddressDisabled() &&
			$this->hasDifferentDeliveryAddress()
		) {
			$session->setValue('delivery_address', $address);
		}

	}


	public function getDeliveryAddressForm() : Form
	{
	
		if(!$this->delivery_address_form) {
			$this->delivery_address_form = $this->getDeliveryAddress()->createForm('cash_desk_customer_delivery_address_form');

			$this->delivery_address_form->field('address_town')->setIsRequired(true);
			$this->delivery_address_form->field('address_town')->setErrorMessages([
				Form_Field::ERROR_CODE_EMPTY => "Please enter town"
			]);

			$this->delivery_address_form->field('address_zip')->setIsRequired(true);
			$this->delivery_address_form->field('address_zip')->setErrorMessages([
				Form_Field::ERROR_CODE_EMPTY => "Please enter ZIP"
			]);

			$this->delivery_address_form->field('address_street_no')->setIsRequired(true);
			$this->delivery_address_form->field('address_street_no')->setErrorMessages([
				Form_Field::ERROR_CODE_EMPTY => "Please enter street and number"
			]);


			if($this->isCompanyOrder()) {
				$this->delivery_address_form->field('first_name')->setIsRequired(false);
				$this->delivery_address_form->field('surname')->setIsRequired(false);

				$this->delivery_address_form->field('company_name')->setIsRequired(true);
				$this->delivery_address_form->field('company_name')->setErrorMessages([
					Form_Field::ERROR_CODE_EMPTY => 'Please enter company name'
				]);

			} else {
				$this->delivery_address_form->field('first_name')->setIsRequired(true);
				$this->delivery_address_form->field('first_name')->setErrorMessages([
					Form_Field::ERROR_CODE_EMPTY => 'Please enter first name'
				]);
				$this->delivery_address_form->field('surname')->setIsRequired(true);
				$this->delivery_address_form->field('surname')->setErrorMessages([
					Form_Field::ERROR_CODE_EMPTY => 'Please enter surname'
				]);

			}

			$this->updateDeliveryAddressForm( $this->delivery_address_form );
		}

		return $this->delivery_address_form;
	}
	

	public function getLoginForm() : Form
	{
		if(!$this->login_form) {
			$password = new Form_Field_Password('password', 'Password:' );
			$password->setIsRequired(true);
			$password->setErrorMessages([
				Form_Field_Password::ERROR_CODE_EMPTY => 'Please enter your password',
			]);


			$this->login_form = new Form('cash_desk_login_form', [$password]);
			$this->login_form->setAction('?action=customer_login');
		}

		return $this->login_form;
	}

	public function catchLoginForm() : bool
	{
		return $this->getLoginForm()->catch();
	}



	public function registerCustomer() : void
	{
		/**
		 * @var CashDesk $this
		 */


		$customer = Customer::getCurrentCustomer();
		if( $customer ) {
			
			
			$updated = false;
			if(!$customer->getPhoneNumber()) {
				$customer->setPhoneNumber( $this->getPhoneWithPrefix() );
				$updated = true;
			}
			if(!trim($customer->getName())) {
				$customer->setFirstName( $this->getBillingAddress()->getFirstName() );
				$customer->setSurname( $this->getBillingAddress()->getSurname() );
				$updated = true;
			}
			
			if($updated) {
				$customer->save();
			}
			
			return;
		}

		if( $this->getNoRegistration() ) {
			return;
		}

		$billing_address = $this->getBillingAddress();

		$customer = new Customer();
		$customer->setShop($this->getShop());

		$customer->setEmail( $this->getEmailAddress() );
		$customer->setPhoneNumber( $this->getPhoneWithPrefix() );

		$customer->setFirstName( $billing_address->getFirstName() );
		$customer->setSurname( $billing_address->getSurname() );

		$customer->setEncryptedPassword( $this->getPassword() );

		$customer->setRegistrationIp( Http_Request::clientIP() );
		$customer->setRegistrationDateTime( Data_DateTime::now() );

		$customer->save();
		
		$ba = $this->getBillingAddress();
		
		$customer->addAddress( $ba );
		$ba->setIsDefault();
		
		if(
			!$this->isDeliveryAddressDisabled() &&
			$this->hasDifferentDeliveryAddress()
		) {
			$customer->addAddress(
				$this->getDeliveryAddress()
			);
		}
		
		Auth::loginUser( $customer );
		$this->getSession()->setValue( 'customer_id', $customer->getId() );
		
	}


	public function saveCustomerAddresses() : void
	{
		/**
		 * @var CashDesk $this
		 */

		$customer = Customer::getCurrentCustomer();
		if($customer) {
			$customer->addAddress(
				$this->getBillingAddress()
			);

			if( !$this->isDeliveryAddressDisabled() ) {
				$customer->addAddress(
					$this->getDeliveryAddress()
				);
			}
		}
	}
	
	
	public static function phoneValidator( Form_Field_Input $field, Config_PerShop $config ) : bool
	{
		$value_raw = $field->getValueRaw();
		$value_raw = preg_replace('/\D/', '', $value_raw);
		
		$field->setValue($value_raw);
		
		$reg_exp = $config->getPhoneValidationRegExp();
		
		if(!preg_match($reg_exp, $value_raw)) {
			$field->setError( Form_Field::ERROR_CODE_INVALID_FORMAT );
			
			return false;
		}
		
		return true;
		
	}
	
	
	
	
	public function updateDeliveryAddressForm( Form $form ) : void
	{
		
		$form->setAction('?action=customer_delivery_address_send');
		
		$form->renderer()->addJsAction('onsubmit', "CashDesk.customer.deliveryAddress.confirm();return false;");
		
		foreach($form->getFields() as $field) {
			$field->input()->addJsAction('onblur', "CashDesk.customer.deliveryAddress.sendField(this);");
		}
	}
	
}
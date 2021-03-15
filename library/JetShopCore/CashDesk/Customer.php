<?php
namespace JetShop;

use Jet\Data_DateTime;
use Jet\Form;
use Jet\Form_Field_Email;
use Jet\Form_Field_Input;
use Jet\Form_Field_Password;
use Jet\Form_Field_RegistrationPassword;
use Jet\Http_Request;
use Jet\Session;

trait Core_CashDesk_Customer
{
	protected ?Form $mail_form = null;

	protected ?Form $set_password_form = null;

	protected ?Form $login_form = null;

	protected ?Form $billing_address_form = null;

	protected ?Form $delivery_address_form = null;
	

	public function getEmailAddress() : string
	{
		/**
		 * @var Session $session
		 */
		$session = $this->getSession();

		return $session->getValue('email_address', '');
	}

	public function setEmailAddress( string $email ) : void
	{
		/**
		 * @var Session $session
		 */
		$session = $this->getSession();

		$session->setValue('email_address', $email);
		$this->setEmailHasBeenSet(true);
	}


	public function getSetEMailForm() : Form
	{
		if(!$this->mail_form) {
			$email = new Form_Field_Email('email', 'E-mail:', $this->getEmailAddress(), true);
			$email->setErrorMessages([
				Form_Field_Email::ERROR_CODE_EMPTY => 'Please enter your e-mail address',
				Form_Field_Email::ERROR_CODE_INVALID_FORMAT => 'Invalid e-mail address format. Plase check it.'
			]);

			$email->setCatcher(function($value) {
				$this->setEmailHasBeenSet($value);
			});

			$this->mail_form = new Form('cash_desk_set_email_form', [$email]);
			$this->mail_form->setAction('?action=customer_set_email');
		}

		return $this->mail_form;
	}

	public function catchSetEMailForm() : bool
	{
		$form = $this->getSetEMailForm();

		if( !$form->catchInput() || !$form->validate() ) {
			return false;
		}
		$form->catchData();

		return true;
	}


	public function setPassword( string $password ) : void
	{
		/**
		 * @var Session $session
		 */
		$session = $this->getSession();

		$this->setNoRegistration( false );
		$this->setCustomerRegisterOrNotBeenSet( true );

		$password = (new Customer())->encryptPassword( $password );

		$session->setValue('password', $password);
	}

	public function getPassword() : string
	{
		/**
		 * @var Session $session
		 */
		$session = $this->getSession();

		$this->setNoRegistration( false );

		return $session->getValue('password', '');
	}

	public function setNoRegistration( bool $state ) : void
	{
		/**
		 * @var Session $session
		 */
		$session = $this->getSession();

		$session->setValue('no_registration', $state);
	}

	public function getNoRegistration() : bool
	{
		/**
		 * @var Session $session
		 */
		$session = $this->getSession();

		return (bool)$session->getValue('no_registration', false);
	}

	public function getSetPasswordForm() : Form
	{
		if(!$this->set_password_form) {
			$password = new Form_Field_RegistrationPassword('password', 'Password:', '', true);
			$password->setPasswordConfirmationLabel('Confirm password:');
			
			$password->setErrorMessages([
				Form_Field_RegistrationPassword::ERROR_CODE_EMPTY => 'Please enter password',
				Form_Field_RegistrationPassword::ERROR_CODE_CHECK_EMPTY => 'Please confirm password',
				Form_Field_RegistrationPassword::ERROR_CODE_CHECK_NOT_MATCH => 'Password confirmation does not match',

			]);

			$password->setCatcher(function($value) {

				$this->setPassword( $value );
			});

			$this->set_password_form = new Form('registration_set_password_form', [$password]);
			$this->set_password_form->setAction('?action=customer_set_password');
		}

		return $this->set_password_form;
	}

	public function catchSetPasswordForm() : bool
	{
		$form = $this->getSetPasswordForm();

		if( !$form->catchInput() || !$form->validate() ) {
			return false;
		}
		$form->catchData();

		return true;
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


	public function getBillingAddressForm() : Form
	{
		/**
		 * @var CashDesk $this
		 */
		if(!$this->billing_address_form) {
			$this->billing_address_form = $this->getBillingAddress()->getCommonForm('cash_desk_customer_billing_address_form');


			$phone = new Form_Field_Input('phone', 'Phone number:', $this->getPhone(), true);
			$phone->setErrorMessages([
				Form_Field_Input::ERROR_CODE_EMPTY => 'Please enter your phone number',
				Form_Field_Input::ERROR_CODE_INVALID_FORMAT => 'Please enter your phone number'
			]);
			$phone->setCatcher(function($value) {
				$this->setPhone($value);
			});

			$this->billing_address_form->addField($phone);



			if($this->isDeliveryAddressDisabled()) {
				$this->billing_address_form->field('address_town')->setIsRequired(false);
				$this->billing_address_form->field('address_zip')->setIsRequired(false);
				$this->billing_address_form->field('address_street_no')->setIsRequired(false);
			} else {
				$this->billing_address_form->field('address_town')->setIsRequired(true);
				$this->billing_address_form->field('address_town')->setErrorMessages([
					Form_Field_Input::ERROR_CODE_EMPTY => "Please enter town"
				]);

				$this->billing_address_form->field('address_zip')->setIsRequired(true);
				$this->billing_address_form->field('address_zip')->setErrorMessages([
					Form_Field_Input::ERROR_CODE_EMPTY => "Please enter ZIP"
				]);

				$this->billing_address_form->field('address_street_no')->setIsRequired(true);
				$this->billing_address_form->field('address_street_no')->setErrorMessages([
					Form_Field_Input::ERROR_CODE_EMPTY => "Please enter street and number"
				]);
			}


			if($this->isCompanyOrder()) {
				$this->billing_address_form->field('first_name')->setIsRequired(false);
				$this->billing_address_form->field('surname')->setIsRequired(false);

				$this->billing_address_form->field('company_name')->setIsRequired(true);
				$this->billing_address_form->field('company_name')->setErrorMessages([
					Form_Field_Input::ERROR_CODE_EMPTY => 'Please enter company name'
				]);

				$this->billing_address_form->field('company_id')->setIsRequired(true);
				$this->billing_address_form->field('company_id')->setErrorMessages([
					Form_Field_Input::ERROR_CODE_EMPTY => 'Please enter company ID'
				]);
			} else {
				$this->billing_address_form->field('first_name')->setIsRequired(true);
				$this->billing_address_form->field('first_name')->setErrorMessages([
					Form_Field_Input::ERROR_CODE_EMPTY => 'Please enter first name'
				]);
				$this->billing_address_form->field('surname')->setIsRequired(true);
				$this->billing_address_form->field('surname')->setErrorMessages([
					Form_Field_Input::ERROR_CODE_EMPTY => 'Please enter surname'
				]);

			}

			$this->getModule()->updateBillingAddressForm( $this, $this->billing_address_form );
		}

		return $this->billing_address_form;
	}

	public function isCompanyOrder() : bool
	{
		/**
		 * @var Session $session
		 */
		$session = $this->getSession();

		return (bool)$session->getValue('is_company_order', false);
	}

	public function setIsCompanyOrder( bool $state ) : void
	{
		/**
		 * @var Session $session
		 */
		$session = $this->getSession();

		$session->setValue('is_company_order', $state);
	}

	public function getPhone() : string
	{
		/**
		 * @var Session $session
		 */
		$session = $this->getSession();

		return $session->getValue('phone', '');
	}

	public function setPhone( string $phone ) : void
	{
		/**
		 * @var Session $session
		 */
		$session = $this->getSession();

		$session->setValue('phone', $phone);
	}


	public function hasDifferentDeliveryAddress() : bool
	{
		/**
		 * @var Session $session
		 */
		$session = $this->getSession();

		return $session->getValue('different_delivery_address',false);
	}

	public function setHasDifferentDeliveryAddress( bool $state ) : void
	{
		/**
		 * @var Session $session
		 */
		$session = $this->getSession();

		$session->setValue('different_delivery_address', $state);
	}




	public function getDeliveryAddress() : ?Customer_Address
	{
		/**
		 * @var Session $session
		 * @var Customer_Address $delivery_address
		 */

		if($this->isDeliveryAddressDisabled()) {
			return null;
		}

		if($this->getSelectedDeliveryMethod()->isPersonalTakeover()) {
			$place = $this->getSelectedPersonalTakeoverPlace();
			$billing_address = $this->getBillingAddress();

			$delivery_address = new Customer_Address();
			$delivery_address->setFirstName( $billing_address->getFirstName() );
			$delivery_address->setSurname( $billing_address->getSurname() );
			$delivery_address->setCompanyName( $place->getName() );
			$delivery_address->setAddressStreetNo( $place->getStreet() );
			$delivery_address->setAddressTown( $place->getTown() );
			$delivery_address->setAddressZip( $place->getZip() );

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
		/**
		 * @var CashDesk $this
		 */
		if(!$this->delivery_address_form) {
			$this->delivery_address_form = $this->getDeliveryAddress()->getCommonForm('cash_desk_customer_delivery_address_form');

			$this->delivery_address_form->field('address_town')->setIsRequired(true);
			$this->delivery_address_form->field('address_town')->setErrorMessages([
				Form_Field_Input::ERROR_CODE_EMPTY => "Please enter town"
			]);

			$this->delivery_address_form->field('address_zip')->setIsRequired(true);
			$this->delivery_address_form->field('address_zip')->setErrorMessages([
				Form_Field_Input::ERROR_CODE_EMPTY => "Please enter ZIP"
			]);

			$this->delivery_address_form->field('address_street_no')->setIsRequired(true);
			$this->delivery_address_form->field('address_street_no')->setErrorMessages([
				Form_Field_Input::ERROR_CODE_EMPTY => "Please enter street and number"
			]);


			if($this->isCompanyOrder()) {
				$this->delivery_address_form->field('first_name')->setIsRequired(false);
				$this->delivery_address_form->field('surname')->setIsRequired(false);

				$this->delivery_address_form->field('company_name')->setIsRequired(true);
				$this->delivery_address_form->field('company_name')->setErrorMessages([
					Form_Field_Input::ERROR_CODE_EMPTY => 'Please enter company name'
				]);

			} else {
				$this->delivery_address_form->field('first_name')->setIsRequired(true);
				$this->delivery_address_form->field('first_name')->setErrorMessages([
					Form_Field_Input::ERROR_CODE_EMPTY => 'Please enter first name'
				]);
				$this->delivery_address_form->field('surname')->setIsRequired(true);
				$this->delivery_address_form->field('surname')->setErrorMessages([
					Form_Field_Input::ERROR_CODE_EMPTY => 'Please enter surname'
				]);

			}

			$this->getModule()->updateDeliveryAddressForm( $this, $this->delivery_address_form );
		}

		return $this->delivery_address_form;
	}


	public function getLoginForm() : Form
	{
		if(!$this->login_form) {
			$password = new Form_Field_Password('password', 'Password:', '', true);
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

	public function onCustomerLogin()
	{
		/**
		 * @var Session $session
		 * @var Customer_Address $billing_address
		 * @var CashDesk $this
		 */
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
			$this->setBillingAddress( $default_address );
		}

		foreach($this->getAgreeFlags() as $flag) {
			$flag->onCustomerLogin( $this );
		}

	}

	public function onCustomerLogout()
	{
		/**
		 * @var Session $session
		 * @var Customer_Address $billing_address
		 * @var CashDesk $this
		 */
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


	public function registerCustomer() : void
	{
		/**
		 * @var CashDesk $this
		 */


		$customer = Customer::getCurrentCustomer();
		if( $customer ) {
			return;
		}

		if( $this->getNoRegistration() ) {
			return;
		}

		$billing_address = $this->getBillingAddress();

		$customer = new Customer();
		$customer->setShopCode($this->getShopCode());

		$customer->setEmail( $this->getEmailAddress() );
		$customer->setPhoneNumber( $this->getPhone() );

		$customer->setFirstName( $billing_address->getFirstName() );
		$customer->setSurname( $billing_address->getSurname() );

		$customer->setEncryptedPassword( $this->getPassword() );

		$customer->setRegistrationIp( Http_Request::clientIP() );
		$customer->setRegistrationDateTime( Data_DateTime::now() );

		$customer->save();

		$customer->login();
	}


	public function saveCustomerAddresses() : void
	{
		/**
		 * @var CashDesk $this
		 */

		$customer = Customer::getCurrentCustomer();
		if($customer) {

			$ba = $this->getBillingAddress();
			$ba->generateHash();

			if(!$customer->hasAddress($ba)) {
				$customer->addAddress($ba);
			}

			$customer->setDefaultAddress($ba);

			if(
			!$this->isDeliveryAddressDisabled()
			) {
				$da = $this->getDeliveryAddress();
				$da->generateHash();

				if(!$customer->hasAddress($da)) {
					$customer->addAddress($da);
				}
			}
		}
	}

}
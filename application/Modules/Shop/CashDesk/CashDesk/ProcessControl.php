<?php
namespace JetApplicationModule\Shop\CashDesk;

use JetApplication\Customer;

trait CashDesk_ProcessControl
{
	public function isReady() : bool
	{
		$step = $this->getSession()->getValue('step', CashDesk::STEP_DELIVERY );

		if(
			$step==CashDesk::STEP_DELIVERY ||
			$step==CashDesk::STEP_PAYMENT
		) {
			return false;
		}

		if(!Customer::getCurrentCustomer()) {
			if(!$this->getEmailHasBeenSet()) {
				return false;
			}

			if(!$this->getCustomerRegisterOrNotBeenSet()) {
				return false;
			}
		}

		if( !$this->getBillingAddressHasBeenSet() ) {
			return false;
		}

		if($this->isDeliveryAddressDisabled()) {
			return true;
		}
		
		if( !$this->getDeliveryAddressHasBeenSet() ) {
			return false;
		}


		return true;
	}

	public function isDone() : bool
	{
		if(!$this->isReady()) {
			return false;
		}
		
		$res = true;
		foreach($this->getAgreeFlags() as $flag) {
			if($flag->isMandatory() && !$flag->isChecked()) {
				$flag->setShowError( true );
				$res = false;
			}
		}

		return $res;
	}


	public function getCurrentStep(): string
	{
		if($this->isReady()) {
			$this->setCurrentStep( CashDesk::STEP_CONFIRM );
		} else {
			if($this->getSession()->getValue('step', CashDesk::STEP_DELIVERY)==CashDesk::STEP_CONFIRM) {
				$this->setCurrentStep( CashDesk::STEP_CUSTOMER );
			}
		}

		return $this->getSession()->getValue('step', CashDesk::STEP_DELIVERY );
	}

	public function setCurrentStep( string $step ): void
	{
		if($step==CashDesk::STEP_DELIVERY) {
			$this->setBillingAddressHasBeenSet( false );
			$this->setDifferentDeliveryAddressHasBeenSet( false );
			$this->setDeliveryAddressHasBeenSet( false );
		}

		$this->getSession()->setValue('step', $step );
	}

	public function getEmailHasBeenSet() : bool
	{
		return $this->getSession()->getValue('email_has_been_set', false);
	}

	public function setEmailHasBeenSet( bool $state ) : void
	{
		if(!$state) {
			$this->setCurrentStep( CashDesk::STEP_CUSTOMER );
		}

		$this->getSession()->setValue('email_has_been_set', $state);
	}
	
	
	public function getPhoneHasBeenSet() : bool
	{
		return $this->getSession()->getValue('phone_has_been_set', false);
	}
	
	public function setPhoneHasBeenSet( bool $state ) : void
	{
		if(!$state) {
			$this->setCurrentStep( CashDesk::STEP_CUSTOMER );
			$this->getSession()->getValue('customer_register_or_not_been_set', false);
		}
		
		$this->getSession()->setValue('phone_has_been_set', $state);
	}
	

	public function getCustomerRegisterOrNotBeenSet() : bool
	{
		return $this->getSession()->getValue('customer_register_or_not_been_set', false);
	}

	public function setCustomerRegisterOrNotBeenSet( bool $state ) : void
	{
		if(!$state) {
			$this->setCurrentStep( CashDesk::STEP_CUSTOMER );
		}

		$this->getSession()->setValue('customer_register_or_not_been_set', $state);
	}

	public function getLoyaltyProgramSet() : bool
	{
		return $this->getSession()->getValue('loyalty_program_set', false);
	}

	public function setLoyaltyProgramSet( bool $state ) : void
	{
		if(!$state) {
			if($this->getCurrentStep()==CashDesk::STEP_CONFIRM) {
				$this->setCurrentStep( CashDesk::STEP_CUSTOMER );
			}
		}

		$this->getSession()->setValue('loyalty_program_set', $state);
	}

	public function getBillingAddressHasBeenSet() : bool
	{
		return $this->getSession()->getValue('billing_address_has_been_set', false);
	}

	public function setBillingAddressHasBeenSet( bool $state ) : void
	{
		if(!$state) {
			if($this->getCurrentStep()==CashDesk::STEP_CONFIRM) {
				$this->setCurrentStep( CashDesk::STEP_CUSTOMER );
			}
		}

		$this->getSession()->setValue('billing_address_has_been_set', $state);
	}

	public function getDifferentDeliveryAddressHasBeenSet() : bool
	{
		return $this->getSession()->getValue('different_delivery_address_has_been_set', false);
	}

	public function setDifferentDeliveryAddressHasBeenSet( bool $state ) : void
	{
		if(!$state) {
			$this->setCurrentStep( CashDesk::STEP_CUSTOMER );
		} else {
			$this->setCurrentStep( CashDesk::STEP_CONFIRM );
		}

		$this->getSession()->setValue('different_delivery_address_has_been_set', $state);
	}

	public function getDeliveryAddressHasBeenSet() : bool
	{
		return $this->getSession()->getValue('delivery_address_has_been_set', false);
	}

	public function setDeliveryAddressHasBeenSet( bool $state ) : void
	{
		if(!$state) {
			if($this->getCurrentStep()==CashDesk::STEP_CONFIRM) {
				$this->setCurrentStep( CashDesk::STEP_CUSTOMER );
			}
		}

		$this->getSession()->setValue('delivery_address_has_been_set', $state);
	}


	public function isBillingAddressEditable() : bool
	{
		return $this->billing_address_editable;
	}

	public function setBillingAddressEditable( bool $state ) : void
	{
		$this->billing_address_editable = $state;
	}

	public function isDeliveryAddressEditable() : bool
	{
		return $this->delivery_address_editable;
	}

	public function setDeliveryAddressEditable( bool $state ) : void
	{
		$this->delivery_address_editable = $state;
	}

	public function isDeliveryAddressDisabled() : bool
	{

		$delivery_method = $this->getSelectedDeliveryMethod();

		if(
			$delivery_method->isPersonalTakeover() ||
			$delivery_method->isEDelivery()
		) {
			return true;
		}

		return false;
	}

}
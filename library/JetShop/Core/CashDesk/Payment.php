<?php
namespace JetShop;

use JetApplication\Payment_Pricing_Module;
use JetApplication\Payment_Pricing_PriceInfo;
use JetApplication\Payment_Method;
use JetApplication\Payment_Method_Option;
use JetApplication\CashDesk;
use JetApplication\CashDesk_Module;

trait Core_CashDesk_Payment {

	protected ?array $available_payment_methods = null;

	protected ?Payment_Pricing_Module $payment_pricing_module = null;

	public function getPaymentPricingModule() : Payment_Pricing_Module
	{
		if(!$this->payment_pricing_module) {
			$this->payment_pricing_module = Payment_Pricing_Module::getModule();
		}

		return $this->payment_pricing_module;
	}

	public function getPaymentPrice( Payment_Method $method ) : Payment_Pricing_PriceInfo
	{
		/**
		 * @var CashDesk $this
		 */
		return $this->getPaymentPricingModule()->getPrice( $this, $method );
	}
	
	/**
	 * @return Payment_Method[]
	 */
	public function getAvailablePaymentMethods() : iterable
	{
		/**
		 * @var CashDesk $this
		 */
		if($this->available_payment_methods===null) {
			$this->available_payment_methods = [];

			$delivery_method = $this->getSelectedDeliveryMethod();

			foreach($delivery_method->getPaymentMethods() as $payment_method ) {
				if($payment_method->getShopData($this->getShop())->isActive()) {
					$this->available_payment_methods[$payment_method->getCode()] = $payment_method;
				}
			}

			foreach($this->available_payment_methods as $code=>$method) {
				$module = $method->getModule();
				if(!$module) {
					continue;
				}

				if(!$module->isEnabledForOrder($this, $method)) {
					unset($this->available_payment_methods[$code]);
				}
			}


			$this->getModule()->sortPaymentMethods( $this, $this->available_payment_methods );

		}

		return $this->available_payment_methods;
	}

	public function getDefaultPaymentMethod() : Payment_Method
	{
		/**
		 * @var CashDesk_Module $module
		 * @var CashDesk $this
		 */
		$methods = $this->getAvailablePaymentMethods();

		return $this->getModule()->getDefaultPaymentMethod( $this, $methods );
	}

	public function getSelectedPaymentMethod() : Payment_Method
	{
		$session = $this->getSession();

		$methods = $this->getAvailablePaymentMethods();

		$default_method = $this->getDefaultPaymentMethod();

		$code = $session->getValue('selected_payment_method', $default_method->getCode());

		if(!isset($methods[$code])) {
			$session->setValue('selected_payment_method', $default_method->getCode());
			$session->setValue('selected_payment_method_option', '');

			return $default_method;
		}

		return $methods[$code];
	}

	public function selectPaymentMethod( string $code ) : bool
	{
		$session = $this->getSession();

		$methods = $this->getAvailablePaymentMethods();

		if(!isset($methods[$code])) {
			return false;
		}
		if($session->getValue('selected_payment_method')!=$code) {
			$session->setValue('selected_payment_method', $code);
			$session->setValue('selected_payment_method_option', '');
		}

		return true;
	}

	public function getSelectedPaymentMethodOption() : ?Payment_Method_Option
	{
		$session = $this->getSession();

		$method = $this->getSelectedPaymentMethod();
		$options = $method->getActiveOptions( $this->getShop() );

		if( !$options ) {
			return null;
		}

		$option_code = $session->getValue('selected_payment_method_option', '');
		if(!isset($options[$option_code])) {
			$option_code = array_keys($options)[0];
		}

		return $options[$option_code];
	}

	public function selectPaymentMethodOption( string $option_code ) : bool
	{
		$session = $this->getSession();

		$method = $this->getSelectedPaymentMethod();
		$options = $method->getActiveOptions( $this->getShop() );

		if(
			!$options ||
			!isset($options[$option_code])
		) {
			return false;
		}

		$session->setValue('selected_payment_method_option', $option_code);

		return true;
	}

}
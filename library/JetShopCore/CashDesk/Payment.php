<?php
namespace JetShop;

use Jet\Session;

trait Core_CashDesk_Payment {

	protected ?array $available_payment_methods = null;

	/**
	 * @return Payment_Method[]
	 */
	public function getAvailablePaymentMethods() : iterable
	{
		if($this->available_payment_methods===null) {
			$this->available_payment_methods = [];

			/**
			 * @var Delivery_Method $delivery_method
			 */
			$delivery_method = $this->getSelectedDeliveryMethod();

			foreach($delivery_method->getPaymentMethods() as $payment_method ) {
				if($payment_method->getShopData($this->shop_code)->isActive()) {
					$this->available_payment_methods[$payment_method->getCode()] = $payment_method;
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
		/**
		 * @var Session $session
		 */
		$session = $this->getSession();

		$methods = $this->getAvailablePaymentMethods();

		$default_method = $this->getDefaultPaymentMethod();

		$code = $session->getValue('selected_payment_method', $default_method->getCode());

		if(!isset($methods[$code])) {
			$session->setValue('selected_payment_method', $default_method->getCode());

			return $default_method;
		}

		return $methods[$code];
	}

	public function selectPaymentMethod( string $code ) : bool
	{
		/**
		 * @var Session $session
		 */
		$session = $this->getSession();

		$methods = $this->getAvailablePaymentMethods();

		if(!isset($methods[$code])) {
			return false;
		}
		if($session->getValue('selected_payment_method')!=$code) {
			$session->setValue('selected_payment_method', $code);
		}

		return true;
	}


}
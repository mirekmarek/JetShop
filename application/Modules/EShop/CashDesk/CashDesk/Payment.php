<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\CashDesk;

use JetApplication\Payment_Method;
use JetApplication\Payment_Method_Option;

trait CashDesk_Payment {
	
	/**
	 * @var Payment_Method[]
	 */
	protected ?array $available_payment_methods = null;
	
	
	/**
	 * @return Payment_Method[]
	 */
	public function getAvailablePaymentMethods() : iterable
	{
	
		if($this->available_payment_methods===null) {
			$this->available_payment_methods = [];

			$delivery_method = $this->getSelectedDeliveryMethod();
			
			if($delivery_method) {
				foreach($delivery_method->getPaymentMethods() as $payment_method ) {
					if($payment_method->isActive()) {
						$this->available_payment_methods[$payment_method->getId()] = $payment_method;
					}
				}
			}
			
			$amount = $this->cart->getAmount();
			foreach($this->available_payment_methods as $i=>$method) {
				if(
					(
						$method->getMinimalOrderAmount() &&
						$amount<$method->getMinimalOrderAmount()
					) ||
					(
						$method->getMaximalOrderAmount() &&
						$amount>$method->getMinimalOrderAmount()
					)
				) {
					unset($this->available_payment_methods[$i]);
				}
			}

			
			foreach($this->available_payment_methods as $code=>$method) {
				$method->getBackendModule()?->init( $method );
			}


			$this->sortPaymentMethods( $this->available_payment_methods );

		}

		return $this->available_payment_methods;
	}
	
	public function sortPaymentMethods( array &$payment_methods ) : void
	{
		uasort( $payment_methods, function( Payment_Method $a, Payment_Method $b ) {
			return $a->getPriority()<=>$b->getPriority();
		} );
		
	}
	

	public function getDefaultPaymentMethod() : ?Payment_Method
	{

		$methods = $this->getAvailablePaymentMethods();
		
		foreach($methods as $payment_method) {
			return $payment_method;
		}
		return null;
	}

	public function getSelectedPaymentMethod() : Payment_Method
	{
		$session = $this->getSession();

		$methods = $this->getAvailablePaymentMethods();

		$default_method = $this->getDefaultPaymentMethod();

		$id = $session->getValue('selected_payment_method', '');

		if(!isset($methods[$id])) {
			$session->setValue('selected_payment_method', $default_method?->getId());
			$session->setValue('selected_payment_method_option', '');

			return $default_method;
		}

		return $methods[$id];
	}

	public function selectPaymentMethod( int $id ) : bool
	{
		$session = $this->getSession();

		$methods = $this->getAvailablePaymentMethods();

		if(!isset( $methods[$id])) {
			return false;
		}
		if($session->getValue('selected_payment_method')!=$id) {
			$session->setValue('selected_payment_method', $id);
			$session->setValue('selected_payment_method_option', '');
		}

		return true;
	}

	public function getSelectedPaymentMethodOption() : ?Payment_Method_Option
	{
		$session = $this->getSession();

		$method = $this->getSelectedPaymentMethod();
		$options = $method->getOptions();

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
		$options = $method->getOptions();

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
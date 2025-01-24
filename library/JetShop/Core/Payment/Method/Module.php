<?php
/**
 *
 */

namespace JetShop;

use Jet\Application_Module;

use JetApplication\Order;
use JetApplication\Payment_Method;
use JetApplication\Payment_Method_EShopData;

abstract class Core_Payment_Method_Module extends Application_Module
{
	public function init( Payment_Method_EShopData $payment_method ) : void
	{
	}
	
	abstract public function handlePayment( Order $order, string $return_url ) : bool;
	
	abstract public function tryAgain( Order $order, string $return_url ) : bool;
	
	abstract public function handlePaymentReturn( Order $order ) : bool;
	
	
	
	public function getOrderConfirmationEmailInfoText( Order $order, Payment_Method_EShopData $payment_method ) : string
	{
		return $payment_method->getConfirmationEmailInfoText();
	}
	
	abstract public function getPaymentMethodSpecificationList() : array;
	
	abstract public function getPaymentMethodOptionsList( Payment_Method|Payment_Method_EShopData $payment_method ) : array;

}
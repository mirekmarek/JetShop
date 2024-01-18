<?php
/**
 *
 */

namespace JetShop;

use Jet\Application_Module;

use JetApplication\Order;
use JetApplication\Payment_Method_ShopData;

abstract class Core_Payment_Method_Module extends Application_Module
{
	public function init( Payment_Method_ShopData $payment_method ) : void
	{
	}
	
	abstract public function handlePayment( Order $order, Payment_Method_ShopData $payment_method ) : bool;
	
	public function getOrderConfirmationEmailInfoText( Order $order, Payment_Method_ShopData $payment_method ) : string
	{
		return $payment_method->getConfirmationEmailInfoText();
	}

}
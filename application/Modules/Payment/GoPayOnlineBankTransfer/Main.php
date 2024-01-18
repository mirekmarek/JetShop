<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Payment\GoPayOnlineBankTransfer;

use JetApplication\Order;
use JetApplication\Payment_Method_Module;
use JetApplication\Payment_Method_ShopData;

class Main extends Payment_Method_Module
{
	public function handlePayment( Order $order, Payment_Method_ShopData $payment_method ): bool
	{
		// TODO: Implement handlePayment() method.
		return false;
	}
}
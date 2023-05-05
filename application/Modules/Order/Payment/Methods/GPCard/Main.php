<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Order\Payment\Methods\GPCard;

use JetApplication\CashDesk;
use JetApplication\Order;
use JetApplication\Payment_Method_Module;

/**
 *
 */
class Main extends Payment_Method_Module
{

	public function getOrderStatusCode( CashDesk $cash_desk ): string
	{
		return 'waiting_for_payment';
	}

	public function handlePayment( Order $order ) : bool
	{
		//TODO:

		return false;
	}
}
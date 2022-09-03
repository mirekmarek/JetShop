<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Order\Payment\Methods\GoPayOnlineBankTransfer;

use JetShop\CashDesk;
use JetShop\Order;
use JetShop\Payment_Method_Module;

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
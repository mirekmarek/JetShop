<?php
/**
 *
 */

namespace JetShop;

use Jet\Application_Module;

use JetApplication\CashDesk;
use JetApplication\Payment_Method;
use JetApplication\Order;

abstract class Core_Payment_Method_Module extends Application_Module
{
	public function getDefaultPrice( CashDesk $cash_desk, Payment_Method $payment_method ) : float
	{
		return $payment_method->getShopData($cash_desk->getShop())->getDefaultPrice();
	}

	public function isEnabledForOrder( CashDesk $cash_desk, Payment_Method $payment_method ) : bool
	{
		return true;
	}

	abstract public function getOrderStatusCode( CashDesk $cash_desk ) : string;

	abstract public function handlePayment( Order $order ) : bool;

	public function getOrderConfirmationEmailInfoText( Order $order, Payment_Method $payment_method ) : string
	{
		return $payment_method->getShopData( $order->getShop() )->getConfirmationEmailInfoText();
	}

}
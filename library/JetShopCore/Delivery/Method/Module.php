<?php
/**
 *
 */

namespace JetShop;

use Jet\Application_Module;

abstract class Core_Delivery_Method_Module extends Application_Module
{

	public function getDefaultPrice( CashDesk $cash_desk, Delivery_Method $delivery_method ) : float
	{
		return $delivery_method->getShopData($cash_desk->getShop())->getDefaultPrice();
	}

	public function isEnabledForOrder( CashDesk $cash_desk, Delivery_Method $deliveryMethod ) : bool
	{
		return true;
	}

	public function getOrderConfirmationEmailInfoText( Order $order, Delivery_Method $deliver_method ) : string
	{
		return $deliver_method->getShopData( $order->getShop() )->getConfirmationEmailInfoText();
	}

}
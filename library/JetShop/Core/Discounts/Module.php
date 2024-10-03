<?php
/**
 *
 */

namespace JetShop;

use Jet\Application_Module;

use JetApplication\CashDesk;
use JetApplication\Order;
use JetApplication\Order_Item;

abstract class Core_Discounts_Module extends Application_Module
{

	abstract public function ShoppingCart_handle() : string;

	abstract public function CashDesk_RegisteredCustomer_handle() : string;

	abstract public function generateDiscounts( CashDesk $cash_desk ) : void;
	
	abstract public function checkDiscounts( CashDesk $cash_desk ) : void;
	

	abstract public function Order_newOrderCreated( Order $order ) : void;

	abstract public function Order_canceled( Order $order ) : void;

	abstract public function Order_itemRemoved( Order $order, Order_Item $item ) : void;

	abstract public function Order_itemAdded( Order $order, Order_Item $item ) : void;

	abstract public function Order_reactivated( Order $order, Order_Item $item ) : void;

}
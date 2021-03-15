<?php
/**
 *
 */

namespace JetShop;

use Jet\Application_Module;

abstract class Core_Discounts_Module extends Application_Module
{

	abstract public function ShoppingCart_handle() : string;

	abstract public function CashDesk_RegisteredCustomer_handle() : string;

	/**
	 * @var CashDesk $cash_desk
	 *
	 * @return Order_Item[]
	 */
	abstract public function getDiscounts( CashDesk $cash_desk ) : array;


	abstract public function Order_saved( Order $order ) : void;

	abstract public function Order_canceled( Order $order ) : void;

	abstract public function Order_itemRemoved( Order $order, Order_Item $item ) : void;

	abstract public function Order_itemAdded( Order $order, Order_Item $item ) : void;

	abstract public function Order_reactivated( Order $order, Order_Item $item ) : void;

}
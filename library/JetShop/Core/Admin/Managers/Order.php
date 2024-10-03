<?php
namespace JetShop;

use JetApplication\Order;
use JetApplication\Customer;

interface Core_Admin_Managers_Order
{
	public function showName( int $id ): string;
	
	public function showOrderStatus( Order $order ) : string;
	
	public function showOrdersOfCustomer( Customer $customer ) : string;
}
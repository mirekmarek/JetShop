<?php
namespace JetShop;

use JetApplication\Admin_EntityManager_Interface;
use JetApplication\Order;
use JetApplication\Customer;

interface Core_Admin_Managers_Order extends Admin_EntityManager_Interface
{
	public function showOrderStatus( Order $order ) : string;
	
	public function showOrdersOfCustomer( Customer $customer ) : string;
}
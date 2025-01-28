<?php
namespace JetShop;


use JetApplication\Admin_EntityManager_Interface;
use JetApplication\Order;

interface Core_Admin_Managers_Invoice extends Admin_EntityManager_Interface
{
	public function showOrderInvoices( Order $order ) : string;
}
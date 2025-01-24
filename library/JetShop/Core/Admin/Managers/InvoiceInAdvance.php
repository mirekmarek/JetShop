<?php
namespace JetShop;


use JetApplication\Admin_EntityManager_WithEShopRelation_Interface;
use JetApplication\Order;

interface Core_Admin_Managers_InvoiceInAdvance extends Admin_EntityManager_WithEShopRelation_Interface
{
	public function showOrderInvoices( Order $order ) : string;
}
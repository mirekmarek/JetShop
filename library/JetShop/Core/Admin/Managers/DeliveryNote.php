<?php
namespace JetShop;


use JetApplication\Order;

interface Core_Admin_Managers_DeliveryNote
{
	public function showName( int $id ): string;
	
	public function showOrderInvoices( Order $order ) : string;
	
}
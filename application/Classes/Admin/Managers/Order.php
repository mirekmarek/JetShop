<?php
namespace JetApplication;

interface Admin_Managers_Order
{
	
	public function showOrderStatus( Shops_Shop $shop, int $status_id ) : string;
	
	public function showOrdersOfCustomer( Customer $customer ) : string;
}
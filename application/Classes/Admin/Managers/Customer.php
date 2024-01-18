<?php
namespace JetApplication;

interface Admin_Managers_Customer
{
	
	public function showLink( int $customer_id ) : string;
	
	public function formatAddress( Shops_Shop $shop, Customer_Address $address ) : string;
	
}
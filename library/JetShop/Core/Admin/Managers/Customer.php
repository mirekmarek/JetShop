<?php
namespace JetShop;

use JetApplication\Entity_Address;
use JetApplication\Shops_Shop;

interface Core_Admin_Managers_Customer
{
	
	public function showName( int $id ) : string;
	
	public function formatAddress( Shops_Shop $shop, Entity_Address $address ) : string;
	
}
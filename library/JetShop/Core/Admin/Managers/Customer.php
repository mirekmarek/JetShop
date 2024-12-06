<?php
namespace JetShop;

use JetApplication\Entity_Address;
use JetApplication\EShop;

interface Core_Admin_Managers_Customer
{
	
	public function showName( int $id ) : string;
	
	public function formatAddress( EShop $eshop, Entity_Address $address ) : string;
	
}
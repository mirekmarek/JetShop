<?php
namespace JetShop;

use JetApplication\Admin_EntityManager_Interface;
use JetApplication\EShopEntity_Address;
use JetApplication\EShop;

interface Core_Admin_Managers_Customer extends Admin_EntityManager_Interface
{
	public function formatAddress( EShop $eshop, EShopEntity_Address $address ) : string;
}
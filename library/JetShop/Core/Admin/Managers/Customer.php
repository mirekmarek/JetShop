<?php
namespace JetShop;

use JetApplication\Admin_EntityManager_Interface;
use JetApplication\Entity_Address;
use JetApplication\EShop;

interface Core_Admin_Managers_Customer extends Admin_EntityManager_Interface
{
	public function formatAddress( EShop $eshop, Entity_Address $address ) : string;
}
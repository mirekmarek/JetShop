<?php
namespace JetShop;

use JetApplication\Admin_Entity_Interface;
use JetApplication\Entity_WithShopData_ShopData;

interface Core_Admin_Entity_WithShopData_Interface extends Admin_Entity_Interface {
	
	public static function getEntityShopDataInstance() : Entity_WithShopData_ShopData;
}
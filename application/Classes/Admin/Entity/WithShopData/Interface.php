<?php
namespace JetApplication;

interface Admin_Entity_WithShopData_Interface extends Admin_Entity_Common_Interface {
	
	public static function getEntityShopDataInstance() : Entity_WithShopData_ShopData;
}
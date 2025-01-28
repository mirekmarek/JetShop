<?php
namespace JetShop;


use JetApplication\Admin_EntityManager_Interface;

interface Core_Admin_Managers_DeliveryMethods extends Admin_EntityManager_Interface
{
	public static function getCurrentUserCanSetPrice() : bool;
}
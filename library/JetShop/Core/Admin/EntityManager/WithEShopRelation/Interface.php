<?php
namespace JetShop;

use JetApplication\Admin_EntityManager_Interface;
use JetApplication\Entity_WithEShopRelation;
use JetApplication\Admin_Entity_WithEShopRelation_Interface;

interface Core_Admin_EntityManager_WithEShopRelation_Interface extends Admin_EntityManager_Interface {
	
	
	public static function getEntityInstance(): Entity_WithEShopRelation|Admin_Entity_WithEShopRelation_Interface;
	
	
}
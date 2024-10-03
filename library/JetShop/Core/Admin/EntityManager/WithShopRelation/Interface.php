<?php
namespace JetShop;

use JetApplication\Admin_EntityManager_Interface;
use JetApplication\Entity_WithShopRelation;
use JetApplication\Admin_Entity_WithShopRelation_Interface;

interface Core_Admin_EntityManager_WithShopRelation_Interface extends Admin_EntityManager_Interface {
	
	
	public static function getEntityInstance(): Entity_WithShopRelation|Admin_Entity_WithShopRelation_Interface;
	
	
}
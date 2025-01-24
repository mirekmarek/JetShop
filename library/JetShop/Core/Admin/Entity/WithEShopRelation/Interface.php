<?php
namespace JetShop;

use Jet\Application_Module;
use JetApplication\Admin_Entity_Interface;
use JetApplication\Admin_EntityManager_WithEShopRelation_Interface;

interface Core_Admin_Entity_WithEShopRelation_Interface extends Admin_Entity_Interface {
	
	public function getAdminManager() : null|Application_Module|Admin_EntityManager_WithEShopRelation_Interface;
	
}
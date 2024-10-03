<?php
namespace JetShop;

use JetApplication\Admin_EntityManager_Interface;
use JetApplication\Entity_Marketing;
use JetApplication\Admin_Entity_Marketing_Interface;

interface Core_Admin_EntityManager_Marketing_Interface extends Admin_EntityManager_Interface {
	
	
	public static function getEntityInstance(): Entity_Marketing|Admin_Entity_Marketing_Interface;
	
	
}
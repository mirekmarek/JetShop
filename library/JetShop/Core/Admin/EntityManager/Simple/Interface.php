<?php
namespace JetShop;

use JetApplication\Admin_EntityManager_Interface;
use JetApplication\Entity_Simple;
use JetApplication\Admin_Entity_Simple_Interface;


interface Core_Admin_EntityManager_Simple_Interface extends Admin_EntityManager_Interface {
	
	public static function getEntityInstance(): Entity_Simple|Admin_Entity_Simple_Interface;
}
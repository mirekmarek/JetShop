<?php
namespace JetShop;

use Jet\Application_Module;
use JetApplication\Admin_Entity_Interface;
use JetApplication\Admin_EntityManager_Common_Interface;

interface Core_Admin_Entity_Common_Interface extends Admin_Entity_Interface {
	
	public function getAdminManager() : null|Application_Module|Admin_EntityManager_Common_Interface;
	
	public function defineImages() : void;
	public function handleImages() : void;
}
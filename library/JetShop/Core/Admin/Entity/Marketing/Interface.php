<?php
namespace JetShop;

use Jet\Application_Module;
use JetApplication\Admin_Entity_Interface;
use JetApplication\Admin_EntityManager_Marketing_Interface;

interface Core_Admin_Entity_Marketing_Interface extends Admin_Entity_Interface {
	
	public function getAdminManager() : null|Application_Module|Admin_EntityManager_Marketing_Interface;
	
	public function hasImages() : bool;
	
	public function handleImages() : void;
	
	public function defineImage( string $image_class, string $image_title ) : void;
}
<?php
namespace JetShop;

use JetApplication\Entity_Basic;
use JetApplication\Admin_Entity_Interface;

interface Core_Admin_EntityManager_Interface {
	
	public static function getName( int $id ) : string;
	
	public function showName( int $id ) : string;
	
	public static function getEditUrl( int $id, array $get_params=[] ) : string;
	
	public static function getCurrentUserCanEdit() : bool;
	
	public static function getCurrentUserCanCreate() : bool;
	
	public static function getCurrentUserCanDelete() : bool;
	
	public static function getEntityInstance(): Entity_Basic|Admin_Entity_Interface;
}
<?php
namespace JetShop;

use JetApplication\Entity_Basic;

interface Core_Admin_EntityManager_Interface {
	
	public function showName( int|object $id_or_item ) : string;
	
	public static function getEntityNameReadable() : string;
	
	public static function getEditUrl( Entity_Basic $item, array $get_params=[] ) : string;
	
	public static function getCurrentUserCanEdit() : bool;
	
	public static function getCurrentUserCanCreate() : bool;
	
	public static function getCurrentUserCanDelete() : bool;
	
}
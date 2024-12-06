<?php
namespace JetShop;

use JetApplication\Admin_EntityManager_Interface;
use JetApplication\Admin_Entity_WithEShopData_Interface;
use JetApplication\Entity_WithEShopData;



interface Core_Admin_EntityManager_WithEShopData_Interface extends Admin_EntityManager_Interface {
	
	public static function getEditUrl( int $id, array $get_params = [] ): string;
	
	public static function showActiveState( int $id ): string;
	
	public static function getEntityInstance(): Entity_WithEShopData|Admin_Entity_WithEShopData_Interface;
	
	public static function getEntityNameReadable() : string;
	
	public function renderActiveState( Entity_WithEShopData $item ) : string;
}
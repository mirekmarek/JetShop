<?php
namespace JetShop;

use JetApplication\Admin_EntityManager_Interface;
use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Entity_WithShopData;



interface Core_Admin_EntityManager_WithShopData_Interface extends Admin_EntityManager_Interface {
	
	public static function showActiveState( int $id ): string;
	
	public static function getEntityInstance(): Entity_WithShopData|Admin_Entity_WithShopData_Interface;
	
	public static function getEntityNameReadable() : string;
	
	public function renderActiveState( Entity_WithShopData $item ) : string;
}
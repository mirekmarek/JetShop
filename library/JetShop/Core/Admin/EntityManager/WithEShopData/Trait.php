<?php
namespace JetShop;

use JetApplication\Admin_EntityManager_Trait;
use JetApplication\Admin_Managers;
use JetApplication\Entity_WithEShopData;


trait Core_Admin_EntityManager_WithEShopData_Trait {
	use Admin_EntityManager_Trait;
	
	public function renderActiveState( Entity_WithEShopData $item ) : string
	{
		return Admin_Managers::EntityEdit_WithEShopData()->renderActiveState( $item );
	}
}
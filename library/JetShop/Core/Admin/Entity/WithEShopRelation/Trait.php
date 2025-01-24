<?php
namespace JetShop;

use Jet\Application_Module;
use JetApplication\Admin_Entity_Trait;
use JetApplication\Admin_EntityManager_WithEShopRelation_Interface;
use JetApplication\Admin_Managers;

trait Core_Admin_Entity_WithEShopRelation_Trait {
	use Admin_Entity_Trait;
	
	public function getAdminManager() : null|Application_Module|Admin_EntityManager_WithEShopRelation_Interface
	{
		$ifc = $this->getAdminManagerInterface();
		if(!$ifc) {
			return null;
		}
		
		return Admin_Managers::get( $ifc );
	}
	
}
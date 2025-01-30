<?php
namespace JetShop;

use Jet\Auth;
use Jet\MVC;
use Jet\MVC_Page_Interface;
use JetApplication\Admin_Managers;
use JetApplication\Auth_Administrator_Role;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_HasActivation_Interface;

trait Core_Admin_EntityManager_Trait {
	
	public static function getAdminMainPageID() : string
	{
		return static::ADMIN_MAIN_PAGE;
	}
	
	public static function getAdminMainPage() : MVC_Page_Interface
	{
		return MVC::getPage( static::getAdminMainPageID() );
	}
	
	public static function getEditUrl( int|EShopEntity_Basic $id_or_item, array $get_params=[] ) : string
	{
		if($id_or_item instanceof EShopEntity_Basic) {
			$id = $id_or_item->getId();
		} else {
			$id = $id_or_item;
		}
		
		$page = static::getAdminMainPage();
		
		$get_params['id'] = $id;
		
		return $page->getURL([], $get_params);
		
	}
	
	public function showName( int|EShopEntity_Basic $id_or_item ): string
	{
		if(is_int($id_or_item)) {
			$id = $id_or_item;
			$item = static::getEntityInstance()::get( $id_or_item );
		} else {
			$item = $id_or_item;
			$id = $item->getId();
		}
		
		return Admin_Managers::EntityEdit()->renderShowName( $id, $item );
	}
	
	public static function getCurrentUserCanEdit() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_UPDATE );
	}
	
	public static function getCurrentUserCanCreate() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_ADD );
	}
	
	public static function getCurrentUserCanDelete() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_DELETE );
	}
	
	public function renderActiveState( EShopEntity_HasActivation_Interface $item ) : string
	{
		return Admin_Managers::EntityEdit()->renderActiveState( $item );
	}
	
}
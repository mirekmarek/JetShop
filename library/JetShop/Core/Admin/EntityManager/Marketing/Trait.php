<?php
namespace JetShop;

use Jet\Auth;
use Jet\MVC;
use JetApplication\Admin_Managers;
use JetApplication\Auth_Administrator_Role;

trait Core_Admin_EntityManager_Marketing_Trait {
	
	public static function getEditUrl( int $id, array $get_params=[] ) : string
	{
		$page = MVC::getPage( static::ADMIN_MAIN_PAGE );
		
		$get_params['id'] = $id;
		
		return $page->getURL([], $get_params);
		
	}
	public static function getName( int $id ) : string
	{
		$item = static::getEntityInstance()::get( $id );
		
		return $item?$item->getAdminTitle():'';
	}
	
	public function showName( int $id ): string
	{
		return Admin_Managers::EntityEdit_Common()->renderShowName( $id, static::getEntityInstance() );
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
	
	
	
}
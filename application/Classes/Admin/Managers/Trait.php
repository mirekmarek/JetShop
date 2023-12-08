<?php
namespace JetApplication;


use Jet\Auth;
use Jet\MVC;

trait Admin_Managers_Trait {
	
	public static function getEditUrl( int|string $is_or_code, array $get_params=[] ) : string
	{
		$page = MVC::getPage( static::ADMIN_MAIN_PAGE );
		
		if(is_string($is_or_code)) {
			$get_params['code'] = $is_or_code;
		} else {
			$get_params['id'] = $is_or_code;
		}
		
		return $page->getURL([], $get_params);
		
	}
	
	public static function getCurrentUserCanEdit() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_UPDATE );
	}
	
	public static function getCurrentUserCanCreate() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_ADD );
	}
	
}
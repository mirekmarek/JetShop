<?php
namespace JetApplicationModule\Admin\Catalog\Brands;

use JetApplication\Admin_Module_Trait;
use JetApplication\Brand_ManageModuleInterface;
use Jet\Application_Module;
use JetApplication\Auth_Administrator_Role;
use Jet\Auth;

/**
 *
 */
class Main extends Application_Module implements Brand_ManageModuleInterface
{
	use Admin_Module_Trait;

	const ADMIN_MAIN_PAGE = 'brands';

	const ACTION_GET_BRAND = 'get_brand';
	const ACTION_ADD_BRAND = 'add_brand';
	const ACTION_UPDATE_BRAND = 'update_brand';
	const ACTION_DELETE_BRAND = 'delete_brand';

	public function getBrandEditUrl( int $id ) : string
	{
		return $this->getEditUrl(
			static::ACTION_GET_BRAND,
			static::ACTION_UPDATE_BRAND,
			static::ADMIN_MAIN_PAGE,
			$id
		);
	}

	public static function getCurrentUserCanEditBrand() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_UPDATE_BRAND );
	}

	public static function getCurrentUserCanCreateBrand() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_ADD_BRAND );
	}

}
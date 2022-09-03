<?php
namespace JetShopModule\Admin\Catalog\Stencils;

use JetShop\Admin_Module_Trait;
use JetShop\Stencil_ManageModuleInterface;
use Jet\Application_Module;
use JetShop\Auth_Administrator_Role;
use Jet\Auth;

/**
 *
 */
class Main extends Application_Module implements Stencil_ManageModuleInterface
{
	use Admin_Module_Trait;

	const ADMIN_MAIN_PAGE = 'stencils';

	const ACTION_GET_STENCIL = 'get_stencil';
	const ACTION_ADD_STENCIL = 'add_stencil';
	const ACTION_UPDATE_STENCIL = 'update_stencil';
	const ACTION_DELETE_STENCIL = 'delete_stencil';

	public function getStencilEditUrl( int $id ) : string
	{
		return $this->getEditUrl(
			static::ACTION_GET_STENCIL,
			static::ACTION_UPDATE_STENCIL,
			static::ADMIN_MAIN_PAGE,
			$id
		);
	}

	public static function getCurrentUserCanEditStencil() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_UPDATE_STENCIL );
	}

	public static function getCurrentUserCanCreateStencil() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_ADD_STENCIL );
	}

}
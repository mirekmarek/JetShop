<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Services\Services;

use Jet\Application_Module;
use Jet\Auth;
use JetApplication\Admin_Module_Trait;
use JetApplication\Auth_Administrator_Role;
use JetApplication\Services_Service_ManageModuleInterface;

/**
 *
 */
class Main extends Application_Module implements Services_Service_ManageModuleInterface
{
	use Admin_Module_Trait;

	const ADMIN_MAIN_PAGE = 'service';

	const ACTION_GET_SERVICE = 'get_service';
	const ACTION_ADD_SERVICE = 'add_service';
	const ACTION_UPDATE_SERVICE = 'update_service';
	const ACTION_DELETE_SERVICE = 'delete_service';


	public function getServiceEditURL( string $id ): string
	{
		return $this->getEditUrl(
			static::ACTION_GET_SERVICE,
			static::ACTION_UPDATE_SERVICE,
			static::ADMIN_MAIN_PAGE,
			$id
		);
	}

	public static function getCurrentUserCanEditService(): bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_UPDATE_SERVICE );
	}

	public static function getCurrentUserCanCreateService(): bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_ADD_SERVICE );
	}

}
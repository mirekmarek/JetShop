<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Delivery\Methods;

use Jet\Application_Module;
use Jet\Auth;
use JetApplication\Admin_Module_Trait;
use JetApplication\Auth_Administrator_Role;
use JetApplication\Delivery_Method_ManageModuleInterface;

/**
 *
 */
class Main extends Application_Module implements Delivery_Method_ManageModuleInterface
{
	use Admin_Module_Trait;

	const ADMIN_MAIN_PAGE = 'delivery-method';

	const ACTION_GET_DELIVERY_METHOD = 'get_delivery_method';
	const ACTION_ADD_DELIVERY_METHOD = 'add_delivery_method';
	const ACTION_UPDATE_DELIVERY_METHOD = 'update_delivery_method';
	const ACTION_DELETE_DELIVERY_METHOD = 'delete_delivery_method';


	public function getDeliveryMethodEditURL( string $id ): string
	{
		return $this->getEditUrl(
			static::ACTION_GET_DELIVERY_METHOD,
			static::ACTION_UPDATE_DELIVERY_METHOD,
			static::ADMIN_MAIN_PAGE,
			$id
		);
	}

	public static function getCurrentUserCanEditDeliveryMethod(): bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_UPDATE_DELIVERY_METHOD );
	}

	public static function getCurrentUserCanCreateDeliveryMethod(): bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_ADD_DELIVERY_METHOD );
	}

}
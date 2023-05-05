<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Delivery\Classes;

use Jet\Application_Module;
use Jet\Auth;
use JetApplication\Admin_Module_Trait;
use JetApplication\Auth_Administrator_Role;
use JetApplication\Delivery_Class_ManageModuleInterface;

/**
 *
 */
class Main extends Application_Module implements Delivery_Class_ManageModuleInterface
{
	use Admin_Module_Trait;

	const ADMIN_MAIN_PAGE = 'delivery-class';

	const ACTION_GET_DELIVERY_CLASS = 'get_delivery_class';
	const ACTION_ADD_DELIVERY_CLASS = 'add_delivery_class';
	const ACTION_UPDATE_DELIVERY_CLASS = 'update_delivery_class';
	const ACTION_DELETE_DELIVERY_CLASS = 'delete_delivery_class';



	public function getDeliveryClassEditURL( string $id ): string
	{
		return $this->getEditUrl(
			static::ACTION_GET_DELIVERY_CLASS,
			static::ACTION_UPDATE_DELIVERY_CLASS,
			static::ADMIN_MAIN_PAGE,
			$id
		);
	}

	public static function getCurrentUserCanEditDeliveryClass(): bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_UPDATE_DELIVERY_CLASS );
	}

	public static function getCurrentUserCanCreateDeliveryClass(): bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_UPDATE_DELIVERY_CLASS );
	}

}
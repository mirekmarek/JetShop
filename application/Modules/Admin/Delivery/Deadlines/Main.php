<?php
namespace JetShopModule\Admin\Delivery\Deadlines;

use JetShop\Admin_Module_Trait;
use JetShop\Delivery_Deadline_ManageModuleInterface;
use Jet\Application_Module;
use JetShop\Auth_Administrator_Role;
use Jet\Auth;

class Main extends Application_Module implements Delivery_Deadline_ManageModuleInterface
{
	use Admin_Module_Trait;

	const ADMIN_MAIN_PAGE = 'delivery_deadlines';

	const ACTION_GET_DELIVERY_DEADLINE = 'get_delivery_deadline';
	const ACTION_ADD_DELIVERY_DEADLINE = 'add_delivery_deadline';
	const ACTION_UPDATE_DELIVERY_DEADLINE = 'update_delivery_deadline';
	const ACTION_DELETE_DELIVERY_DEADLINE = 'delete_delivery_deadline';


	public function getDeliveryTermEditUrl( string $code ) : string
	{
		return $this->getEditUrl(
			static::ACTION_GET_DELIVERY_DEADLINE,
			static::ACTION_UPDATE_DELIVERY_DEADLINE,
			static::ADMIN_MAIN_PAGE,
			$code
		);
	}

	public static function getCurrentUserCanEditDeliveryTerm() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_UPDATE_DELIVERY_DEADLINE );
	}

	public static function getCurrentUserCanCreateDeliveryTerm() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_ADD_DELIVERY_DEADLINE );
	}

}
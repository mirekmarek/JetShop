<?php
namespace JetShopModule\Admin\Catalog\DeliveryTerms;

use JetShop\Admin_Module_Trait;
use JetShop\DeliveryTerm_ManageModuleInterface;
use Jet\Application_Module;
use JetShop\Auth_Administrator_Role;
use Jet\Auth;

class Main extends Application_Module implements DeliveryTerm_ManageModuleInterface
{
	use Admin_Module_Trait;

	const ADMIN_MAIN_PAGE = 'delivery_terms';

	const ACTION_GET_DELIVERY_TERM = 'get_delivery_term';
	const ACTION_ADD_DELIVERY_TERM = 'add_delivery_term';
	const ACTION_UPDATE_DELIVERY_TERM = 'update_delivery_term';
	const ACTION_DELETE_DELIVERY_TERM = 'delete_delivery_term';


	public function getDeliveryTermEditUrl( int $id ) : string
	{
		return $this->getEditUrl(
			static::ACTION_GET_DELIVERY_TERM,
			static::ACTION_UPDATE_DELIVERY_TERM,
			static::ADMIN_MAIN_PAGE,
			$id
		);
	}

	public static function getCurrentUserCanEditDeliveryTerm() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_UPDATE_DELIVERY_TERM );
	}

	public static function getCurrentUserCanCreateDeliveryTerm() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_ADD_DELIVERY_TERM );
	}

}
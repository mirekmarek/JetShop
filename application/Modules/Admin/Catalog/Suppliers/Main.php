<?php
namespace JetApplicationModule\Admin\Catalog\Suppliers;

use JetApplication\Admin_Module_Trait;
use JetApplication\Supplier_ManageModuleInterface;
use Jet\Application_Module;
use JetApplication\Auth_Administrator_Role;
use Jet\Auth;

/**
 *
 */
class Main extends Application_Module implements Supplier_ManageModuleInterface
{
	use Admin_Module_Trait;

	const ADMIN_MAIN_PAGE = 'suppliers';

	const ACTION_GET_SUPPLIER = 'get_supplier';
	const ACTION_ADD_SUPPLIER = 'add_supplier';
	const ACTION_UPDATE_SUPPLIER = 'update_supplier';
	const ACTION_DELETE_SUPPLIER = 'delete_supplier';


	public function getSupplierEditUrl( int $id ) : string
	{
		return $this->getEditUrl(
			static::ACTION_GET_SUPPLIER,
			static::ACTION_UPDATE_SUPPLIER,
			static::ADMIN_MAIN_PAGE,
			$id
		);
	}

	public static function getCurrentUserCanEditSupplier() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_UPDATE_SUPPLIER );
	}

	public static function getCurrentUserCanCreateSupplier() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_ADD_SUPPLIER );
	}

}
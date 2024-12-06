<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Payment\Methods;

use Jet\Application_Module;
use Jet\Auth;
use JetApplication\Admin_EntityManager_WithEShopData_Interface;
use JetApplication\Admin_EntityManager_WithEShopData_Trait;
use JetApplication\Admin_Entity_WithEShopData_Interface;
use JetApplication\Auth_Administrator_Role;
use JetApplication\Entity_WithEShopData;

/**
 *
 */
class Main extends Application_Module implements Admin_EntityManager_WithEShopData_Interface
{
	use Admin_EntityManager_WithEShopData_Trait;

	public const ADMIN_MAIN_PAGE = 'payment-method';

	public const ACTION_GET = 'get_payment_method';
	public const ACTION_ADD = 'add_payment_method';
	public const ACTION_UPDATE = 'update_payment_method';
	public const ACTION_DELETE = 'delete_payment_method';
	public const ACTION_SET_PRICE = 'set_price';
	
	public static function getEntityInstance(): Entity_WithEShopData|Admin_Entity_WithEShopData_Interface
	{
		return new PaymentMethod();
	}
	
	public static function getCurrentUserCanSetPrice() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_SET_PRICE );
	}
	
	public static function getEntityNameReadable() : string
	{
		return 'payment method';
	}

}
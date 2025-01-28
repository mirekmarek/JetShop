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
use JetApplication\Admin_EntityManager_Trait;
use JetApplication\Admin_Managers_PaymentMethods;
use JetApplication\Auth_Administrator_Role;
use JetApplication\Entity_Basic;
use JetApplication\Payment_Method;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_PaymentMethods
{
	use Admin_EntityManager_Trait;

	public const ADMIN_MAIN_PAGE = 'payment-method';

	public const ACTION_GET = 'get_payment_method';
	public const ACTION_ADD = 'add_payment_method';
	public const ACTION_UPDATE = 'update_payment_method';
	public const ACTION_DELETE = 'delete_payment_method';
	public const ACTION_SET_PRICE = 'set_price';
	
	public static function getEntityInstance(): Entity_Basic
	{
		return new Payment_Method();
	}
	
	public static function getCurrentUserCanSetPrice() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_SET_PRICE );
	}
	

}
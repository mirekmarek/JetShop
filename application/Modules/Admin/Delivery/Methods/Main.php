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
use JetApplication\Admin_Managers_DeliveryMethods;
use JetApplication\Admin_EntityManager_Trait;
use JetApplication\Auth_Administrator_Role;
use JetApplication\Delivery_Method;
use JetApplication\EShopEntity_Basic;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_DeliveryMethods
{
	use Admin_EntityManager_Trait;

	public const ADMIN_MAIN_PAGE = 'delivery-method';

	public const ACTION_GET = 'get_delivery_method';
	public const ACTION_ADD = 'add_delivery_method';
	public const ACTION_UPDATE = 'update_delivery_method';
	public const ACTION_DELETE = 'delete_delivery_method';
	public const ACTION_SET_PRICE = 'set_price';
	
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Delivery_Method();
	}
	
	public static function getCurrentUserCanSetPrice() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_SET_PRICE );
	}
	
}
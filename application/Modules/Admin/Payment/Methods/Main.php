<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Payment\Methods;

use Jet\Application_Module;
use JetApplication\Admin_Entity_WithShopData_Manager_Interface;
use JetApplication\Admin_Entity_WithShopData_Manager_Trait;
use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Entity_WithShopData;

/**
 *
 */
class Main extends Application_Module implements Admin_Entity_WithShopData_Manager_Interface
{
	use Admin_Entity_WithShopData_Manager_Trait;

	public const ADMIN_MAIN_PAGE = 'payment-method';

	public const ACTION_GET = 'get_payment_method';
	public const ACTION_ADD = 'add_payment_method';
	public const ACTION_UPDATE = 'update_payment_method';
	public const ACTION_DELETE = 'delete_payment_method';
	
	public static function getEntityInstance(): Entity_WithShopData|Admin_Entity_WithShopData_Interface
	{
		return new PaymentMethod();
	}
	
	public static function getEntityNameReadable() : string
	{
		return 'payment method';
	}

}
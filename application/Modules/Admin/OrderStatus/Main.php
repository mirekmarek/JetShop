<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\OrderStatus;

use Jet\Application_Module;
use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Admin_Entity_WithShopData_Manager_Interface;
use JetApplication\Admin_Entity_WithShopData_Manager_Trait;
use JetApplication\Entity_WithShopData;

/**
 *
 */
class Main extends Application_Module implements Admin_Entity_WithShopData_Manager_Interface
{
	use Admin_Entity_WithShopData_Manager_Trait;
	
	
	public const ADMIN_MAIN_PAGE = 'order-status';

	public const ACTION_GET = 'get_order_status';
	public const ACTION_ADD = 'add_order_status';
	public const ACTION_UPDATE = 'update_order_status';
	public const ACTION_DELETE = 'delete_order_status';
	
	
	public static function getEntityInstance(): Entity_WithShopData|Admin_Entity_WithShopData_Interface
	{
		return new OrderStatus();
	}
	
	public static function getEntityNameReadable() : string
	{
		return 'order status';
	}

}
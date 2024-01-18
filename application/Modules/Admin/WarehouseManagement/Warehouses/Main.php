<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\WarehouseManagement\Warehouses;

use Jet\Application_Module;
use JetApplication\Admin_Entity_Common_Interface;
use JetApplication\Admin_Entity_Common_Manager_Interface;
use JetApplication\Admin_Entity_Common_Manager_Trait;
use JetApplication\Entity_Common;

/**
 *
 */
class Main extends Application_Module implements Admin_Entity_Common_Manager_Interface
{
	use Admin_Entity_Common_Manager_Trait;
	
	public const ADMIN_MAIN_PAGE = 'warehouses';

	public const ACTION_GET = 'get_warehouse';
	public const ACTION_ADD = 'add_warehouse';
	public const ACTION_UPDATE = 'update_warehouse';
	public const ACTION_DELETE = 'delete_warehouse';
	
	
	public static function getEntityInstance(): Entity_Common|Admin_Entity_Common_Interface
	{
		return new WareHouse();
	}
	
	public static function getEntityNameReadable() : string
	{
		return 'ware house';
	}

}
<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\WarehouseManagement\Warehouses;

use Jet\Application_Module;
use JetApplication\Admin_EntityManager_Trait;
use JetApplication\Admin_Managers_WarehouseManagementWarehouses;
use JetApplication\Entity_Basic;
use JetApplication\WarehouseManagement_Warehouse;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_WarehouseManagementWarehouses
{
	use Admin_EntityManager_Trait;
	
	public const ADMIN_MAIN_PAGE = 'warehouses';

	public const ACTION_GET = 'get_warehouse';
	public const ACTION_ADD = 'add_warehouse';
	public const ACTION_UPDATE = 'update_warehouse';
	public const ACTION_DELETE = 'delete_warehouse';
	
	
	public static function getEntityInstance(): Entity_Basic
	{
		return new WarehouseManagement_Warehouse();
	}
}
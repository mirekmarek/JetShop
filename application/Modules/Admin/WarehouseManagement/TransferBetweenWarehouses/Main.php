<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\WarehouseManagement\TransferBetweenWarehouses;

use Jet\Application_Module;
use JetApplication\Admin_EntityManager_Simple_Trait;
use JetApplication\Admin_Managers_WarehouseManagementTransferTransferBetweenWarehouses;
use JetApplication\Entity_Simple;
use JetApplication\Admin_Entity_Simple_Interface;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_WarehouseManagementTransferTransferBetweenWarehouses
{
	use Admin_EntityManager_Simple_Trait;
	
	public const ADMIN_MAIN_PAGE = 'transfer-between-warehouses';
	
	public const ACTION_GET = 'get_transfer_between_warehouses';
	public const ACTION_ADD = 'add_transfer_between_warehouses';
	public const ACTION_UPDATE = 'update_transfer_between_warehouses';
	public const ACTION_DELETE = 'delete_transfer_between_warehouses';
	
	
	public static function getEntityInstance(): Entity_Simple|Admin_Entity_Simple_Interface
	{
		return new WarehouseManagement_TransferBetweenWarehouses();
	}
	
	public static function getEntityNameReadable() : string
	{
		return 'Warehouse Management - Transfer between warehouses';
	}
	
	public static function getCurrentUserCanDelete() : bool
	{
		return false;
	}
	
}
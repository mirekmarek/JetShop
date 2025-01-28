<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\WarehouseManagement\TransferBetweenWarehouses;

use Jet\Application_Module;
use JetApplication\Admin_EntityManager_Trait;
use JetApplication\Admin_Managers_WarehouseManagementTransferTransferBetweenWarehouses;
use JetApplication\Entity_Basic;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_WarehouseManagementTransferTransferBetweenWarehouses
{
	use Admin_EntityManager_Trait;
	
	public const ADMIN_MAIN_PAGE = 'transfer-between-warehouses';
	
	public const ACTION_GET = 'get_transfer_between_warehouses';
	public const ACTION_ADD = 'add_transfer_between_warehouses';
	public const ACTION_UPDATE = 'update_transfer_between_warehouses';
	public const ACTION_DELETE = 'delete_transfer_between_warehouses';
	
	
	public static function getEntityInstance(): Entity_Basic
	{
		return new WarehouseManagement_TransferBetweenWarehouses();
	}
	
	public static function getCurrentUserCanDelete() : bool
	{
		return false;
	}
	
}
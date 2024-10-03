<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\WarehouseManagement\TransferBetweenWarehouses;

use Jet\Application_Module;
use JetApplication\Admin_EntityManager_Simple_Interface;
use JetApplication\Admin_EntityManager_Simple_Trait;
use JetApplication\Admin_Managers_TransferBetweenWarehouses;
use JetApplication\Entity_Simple;
use JetApplication\Admin_Entity_Simple_Interface;

/**
 *
 */
class Main extends Application_Module implements Admin_EntityManager_Simple_Interface, Admin_Managers_TransferBetweenWarehouses
{
	use Admin_EntityManager_Simple_Trait;
	
	public const ADMIN_MAIN_PAGE = 'transfer-between-warehouses';
	
	public const ACTION_GET = 'get_transfer_between_warehouses';
	public const ACTION_ADD = 'add_transfer_between_warehouses';
	public const ACTION_UPDATE = 'update_transfer_between_warehouses';
	public const ACTION_DELETE = 'delete_transfer_between_warehouses';
	
	
	public static function getEntityInstance(): Entity_Simple|Admin_Entity_Simple_Interface
	{
		return new Transfer();
	}
	
	public static function getEntityNameReadable() : string
	{
		return 'Transfer between warehouses';
	}
	
	public static function getCurrentUserCanDelete() : bool
	{
		return false;
	}
	
}
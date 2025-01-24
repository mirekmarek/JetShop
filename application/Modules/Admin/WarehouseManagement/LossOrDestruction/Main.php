<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\WarehouseManagement\LossOrDestruction;

use Jet\Application_Module;
use JetApplication\Admin_EntityManager_Simple_Trait;
use JetApplication\Admin_Managers_WarehouseManagementLossOrDestruction;
use JetApplication\Entity_Simple;
use JetApplication\Admin_Entity_Simple_Interface;
use JetApplication\WarehouseManagement_LossOrDestruction;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_WarehouseManagementLossOrDestruction
{
	use Admin_EntityManager_Simple_Trait;
	
	public const ADMIN_MAIN_PAGE = 'loss-or-destruction';
	
	public const ACTION_GET = 'get_loss_or_destruction';
	public const ACTION_ADD = 'add_loss_or_destruction';
	public const ACTION_UPDATE = 'update_loss_or_destruction';
	public const ACTION_DELETE = 'delete_loss_or_destruction';
	
	
	public static function getEntityInstance(): Entity_Simple|Admin_Entity_Simple_Interface
	{
		return new WarehouseManagement_LossOrDestruction();
	}
	
	public static function getEntityNameReadable() : string
	{
		return 'Warehouse Management - Loss or destruction';
	}
	
	public static function getCurrentUserCanDelete() : bool
	{
		return false;
	}
	
}
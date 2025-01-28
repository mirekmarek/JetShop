<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\WarehouseManagement\LossOrDestruction;

use Jet\Application_Module;
use JetApplication\Admin_EntityManager_Trait;
use JetApplication\Admin_Managers_WarehouseManagementLossOrDestruction;
use JetApplication\Entity_Basic;
use JetApplication\WarehouseManagement_LossOrDestruction;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_WarehouseManagementLossOrDestruction
{
	use Admin_EntityManager_Trait;
	
	public const ADMIN_MAIN_PAGE = 'loss-or-destruction';
	
	public const ACTION_GET = 'get_loss_or_destruction';
	public const ACTION_ADD = 'add_loss_or_destruction';
	public const ACTION_UPDATE = 'update_loss_or_destruction';
	public const ACTION_DELETE = 'delete_loss_or_destruction';
	
	
	public static function getEntityInstance(): Entity_Basic
	{
		return new WarehouseManagement_LossOrDestruction();
	}
	
	public static function getCurrentUserCanDelete() : bool
	{
		return false;
	}
	
}
<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\WarehouseManagement\LossOrDestruction;

use Jet\Application_Module;
use JetApplication\Admin_EntityManager_Simple_Interface;
use JetApplication\Admin_EntityManager_Simple_Trait;
use JetApplication\Admin_Managers_LossOrDestruction;
use JetApplication\Entity_Simple;
use JetApplication\Admin_Entity_Simple_Interface;

/**
 *
 */
class Main extends Application_Module implements Admin_EntityManager_Simple_Interface, Admin_Managers_LossOrDestruction
{
	use Admin_EntityManager_Simple_Trait;
	
	public const ADMIN_MAIN_PAGE = 'loss-or-destruction';
	
	public const ACTION_GET = 'get_loss_or_destruction';
	public const ACTION_ADD = 'add_loss_or_destruction';
	public const ACTION_UPDATE = 'update_loss_or_destruction';
	public const ACTION_DELETE = 'delete_loss_or_destruction';
	
	
	public static function getEntityInstance(): Entity_Simple|Admin_Entity_Simple_Interface
	{
		return new LossOrDestruction();
	}
	
	public static function getEntityNameReadable() : string
	{
		return 'Loss or destruction';
	}
	
	public static function getCurrentUserCanDelete() : bool
	{
		return false;
	}
	
}
<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\WarehouseManagement\StockVerification;

use Jet\Application_Module;
use JetApplication\Admin_EntityManager_Simple_Interface;
use JetApplication\Admin_EntityManager_Simple_Trait;
use JetApplication\Entity_Simple;
use JetApplication\Admin_Entity_Simple_Interface;

/**
 *
 */
class Main extends Application_Module implements Admin_EntityManager_Simple_Interface
{
	use Admin_EntityManager_Simple_Trait;
	
	public const ADMIN_MAIN_PAGE = 'stock-verification';
	
	public const ACTION_GET = 'get_stock_verification';
	public const ACTION_ADD = 'add_stock_verification';
	public const ACTION_UPDATE = 'update_stock_verification';
	public const ACTION_DELETE = 'delete_stock_verification';
	
	
	public static function getEntityInstance(): Entity_Simple|Admin_Entity_Simple_Interface
	{
		return new Verification();
	}
	
	public static function getEntityNameReadable() : string
	{
		return 'Stock verification';
	}
	
	public static function getCurrentUserCanDelete() : bool
	{
		return false;
	}
	
}
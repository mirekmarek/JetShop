<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\WarehouseManagement\StockVerification;

use Jet\Application_Module;
use JetApplication\Admin_EntityManager_Trait;
use JetApplication\Admin_Managers_WarehouseManagementStockVerification;
use JetApplication\Entity_Basic;
use JetApplication\WarehouseManagement_StockVerification;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_WarehouseManagementStockVerification
{
	use Admin_EntityManager_Trait;
	
	public const ADMIN_MAIN_PAGE = 'stock-verification';
	
	public const ACTION_GET = 'get_stock_verification';
	public const ACTION_ADD = 'add_stock_verification';
	public const ACTION_UPDATE = 'update_stock_verification';
	public const ACTION_DELETE = 'delete_stock_verification';
	
	
	public static function getEntityInstance(): Entity_Basic
	{
		return new WarehouseManagement_StockVerification();
	}
	
	public static function getCurrentUserCanDelete() : bool
	{
		return false;
	}
	
}
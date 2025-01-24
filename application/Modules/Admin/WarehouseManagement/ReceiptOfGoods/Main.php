<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\WarehouseManagement\ReceiptOfGoods;

use Jet\Application_Module;
use JetApplication\Admin_EntityManager_Simple_Interface;
use JetApplication\Admin_EntityManager_Simple_Trait;
use JetApplication\Admin_Managers_ReceiptOfGoods;
use JetApplication\Entity_Simple;
use JetApplication\Admin_Entity_Simple_Interface;
use JetApplication\WarehouseManagement_ReceiptOfGoods;

/**
 *
 */
class Main extends Application_Module implements Admin_EntityManager_Simple_Interface, Admin_Managers_ReceiptOfGoods
{
	use Admin_EntityManager_Simple_Trait;
	
	public const ADMIN_MAIN_PAGE = 'receipt-of-goods';
	
	public const ACTION_GET = 'get_receipt_of_goods';
	public const ACTION_ADD = 'add_receipt_of_goods';
	public const ACTION_UPDATE = 'update_receipt_of_goods';
	public const ACTION_DELETE = 'delete_receipt_of_goods';
	
	
	public static function getEntityInstance(): Entity_Simple|Admin_Entity_Simple_Interface
	{
		return new WarehouseManagement_ReceiptOfGoods();
	}
	
	public static function getEntityNameReadable() : string
	{
		return 'Warehouse Management - Receipt Of Goods';
	}
	
	public static function getCurrentUserCanDelete() : bool
	{
		return false;
	}
	
}
<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\WarehouseManagement\ReceiptOfGoods;

use Jet\Application_Module;
use JetApplication\Admin_EntityManager_Interface;
use JetApplication\Admin_EntityManager_Trait;
use JetApplication\Admin_Managers_ReceiptOfGoods;
use JetApplication\EShopEntity_Basic;
use JetApplication\WarehouseManagement_ReceiptOfGoods;

/**
 *
 */
class Main extends Application_Module implements Admin_EntityManager_Interface, Admin_Managers_ReceiptOfGoods
{
	use Admin_EntityManager_Trait;
	
	public const ADMIN_MAIN_PAGE = 'receipt-of-goods';
	
	public const ACTION_GET = 'get_receipt_of_goods';
	public const ACTION_ADD = 'add_receipt_of_goods';
	public const ACTION_UPDATE = 'update_receipt_of_goods';
	public const ACTION_DELETE = 'delete_receipt_of_goods';
	
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new WarehouseManagement_ReceiptOfGoods();
	}
	
	public static function getCurrentUserCanDelete() : bool
	{
		return false;
	}
	
}
<?php
namespace JetApplicationModule\Admin\SupplierGoodsOrders;

use JetApplication\Admin_EntityManager_Simple_Trait;
use Jet\Application_Module;
use JetApplication\Admin_Entity_Simple_Interface;
use JetApplication\Entity_Simple;
use JetApplication\Admin_Managers_SupplierGoodsOrders;
use JetApplication\Supplier_GoodsOrder;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_SupplierGoodsOrders
{
	use Admin_EntityManager_Simple_Trait;

	public const ADMIN_MAIN_PAGE = 'supplier-goods-orders';

	public const ACTION_GET = 'get_supplier_order';
	public const ACTION_ADD = 'add_supplier_order';
	public const ACTION_UPDATE = 'update_supplier_order';
	public const ACTION_DELETE = 'delete_supplier_order';
	
	
	public static function getEntityInstance(): Entity_Simple|Admin_Entity_Simple_Interface
	{
		return new Supplier_GoodsOrder();
	}
	
	public static function getEntityNameReadable() : string
	{
		return 'Order goods from supplier';
	}
	
	public static function getCurrentUserCanDelete() : bool
	{
		return false;
	}

}
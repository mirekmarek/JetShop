<?php
namespace JetShop;

use Jet\Application_Modules;

abstract class Core_WarehouseManagement {

	public static string $order_manage_module_name = 'WarehouseManagement.Manage';

	/**
	 * @return string
	 */
	public static function getOrderManageModuleName(): string
	{
		return static::$order_manage_module_name;
	}

	/**
	 * @param string $order_manage_module_name
	 */
	public static function setOrderManageModuleName( string $order_manage_module_name ): void
	{
		static::$order_manage_module_name = $order_manage_module_name;
	}

	public static function getOrderManageModule() : WarehouseManagement_ManageModule
	{
		/**
		 * @var WarehouseManagement_ManageModule $module
		 */
		$module = Application_Modules::moduleInstance( WarehouseManagement::getOrderManageModuleName() );

		return $module;
	}

	public static function selectWarehousesForOrder( Order $order ) {
		WarehouseManagement::getOrderManageModule()->selectWarehousesForOrder( $order );
	}

	public static function itemRecalculate( string $warehouse_code, int $product_id ) : void
	{
		WarehouseManagement_Item::get( $warehouse_code, $product_id )->recalculate();
	}

	public static function recalculateProductAvailability( int $product_id ) : void
	{
		WarehouseManagement::getOrderManageModule()->recalculateProductAvailability( $product_id );
	}
}
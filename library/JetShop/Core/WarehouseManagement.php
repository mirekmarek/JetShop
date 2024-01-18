<?php
namespace JetShop;


use JetApplication\Order;

abstract class Core_WarehouseManagement {



	public static function selectWarehousesForOrder( Order $order ) : void {
		//TODO: WarehouseManagement::getOrderManageModule()->selectWarehousesForOrder( $order );
	}

	public static function itemRecalculate( string $warehouse_code, int $product_id ) : void
	{
		//TODO: WarehouseManagement_Item::get( $warehouse_code, $product_id )->recalculate();
	}

	public static function recalculateProductAvailability( int $product_id ) : void
	{
		//TODO: WarehouseManagement::getOrderManageModule()->recalculateProductAvailability( $product_id );
	}
}
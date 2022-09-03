<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\WarehouseManagement\Manage;

use JetShop\Order;
use JetShop\Order_Item;
use JetShop\Product;
use JetShop\Shops;
use JetShop\WarehouseManagement_Item;
use JetShop\WarehouseManagement_ManageModule;
use JetShop\WarehouseManagement_Warehouse;

/**
 *
 */
class Main extends WarehouseManagement_ManageModule
{

	public function selectWarehousesForOrder( Order $order ) : void
	{
		/**
		 * @var Order_Item[] $products
		 */
		$products = [];
		$warehouses = [];
		$avl = [];

		foreach(WarehouseManagement_Warehouse::getList() as $warehouse) {
			if(in_array($order->getShopKey(), $warehouse->getShopKeys())) {
				$warehouses[$warehouse->getCode()] = $warehouse;
				$avl[$warehouse->getCode()] = 0;
			}
		}

		foreach($order->getItems() as $item) {
			if($item->getType()!=Order_Item::ITEM_TYPE_PRODUCT) {
				continue;
			}

			$p_id = $item->getProductId();

			foreach($warehouses as $warehouse_code=>$warehouse) {
				$item = WarehouseManagement_Item::get($warehouse_code, $p_id);

				$avl[$warehouse_code] += $item->getAvailable();
			}

			$products[] = $item;
		}

		arsort($avl, SORT_NUMERIC);
		$warehouse_code = array_keys($avl)[0];

		foreach($products as $item) {
			$item->setWarehouseCode($warehouse_code);
		}
	}

	public function recalculateProductAvailability( int $product_id ): void
	{
		$product = Product::get($product_id);
		if(!$product) {
			return;
		}

		$items = WarehouseManagement_Item::getByProduct( $product_id );

		$counts = [];
		foreach(Shops::getList() as $shop) {
			$counts[$shop->getKey()] = 0;
		}

		foreach($items as $item) {
			$warehouse = $item->getWarehouse();

			foreach( $warehouse->getShopKeys() as $shop_key ) {
				$counts[$shop_key] += $item->getAvailable();
			}
		}

		$updated = false;
		foreach($counts as $shop_key=>$count) {
			$shop = Shops::get( $shop_key );

			$shd = $product->getShopData($shop);
			if(
				$shd &&
				$shd->getStockStatus()!=$count
			) {
				$shd->setStockStatus($count);
				$updated = true;
			}
		}

		if($updated) {
			$product->save();
		}
	}
}
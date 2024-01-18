<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\WarehouseManagement\Manage;

use JetApplication\Order;
use JetApplication\Order_Item;
use JetApplication\Product;
use JetApplication\Shops;
use JetApplication\WarehouseManagement_Item;
use JetApplication\WarehouseManagement_Warehouse;

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

			$p_id = $item->getItemId();

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


	//TODO: warehouse management job
	/*
	<div class="row">
		<label class="col-form-label col-md-2"><?=Tr::_('Stock status:')?></label>
		<div class="col-md-8">
			<table class="table table-striped">
				<tr>
					<td style="width: 150px;font-weight: bolder;"><?=Tr::_('Total available:')?></td>
					<td style="font-weight: bolder;"><?=$product->getShopData($shop)->getInStockQty()?></td>
				</tr>
				<?php foreach(WarehouseManagement_Warehouse::getList() as $warehouse):
					if(!$warehouse->hasShop($shop)) {
						continue;
					}
					
					$item = WarehouseManagement_Item::get($warehouse->getCode(), $product->getId());
					?>
					<tr>
						<td colspan="2">
							<b><?=$warehouse->getInternalName()?></b>
						</td>
					</tr>
					
					<tr>
						<td><?=Tr::_('Available:')?></td>
						<td><?=Locale::getCurrentLocale()->formatInt($item->getAvailable())?></td>
					</tr>
					<tr>
						<td><?=Tr::_('In stock:')?></td>
						<td><?=Locale::getCurrentLocale()->formatInt($item->getInStock())?></td>
					</tr>
					<tr>
						<td><?=Tr::_('Blocked:')?></td>
						<td><?=Locale::getCurrentLocale()->formatInt($item->getBlocked())?></td>
					</tr>
					<tr>
						<td><?=Tr::_('Required:')?></td>
						<td><?=Locale::getCurrentLocale()->formatInt($item->getRequired())?></td>
					</tr>
				<?php endforeach; ?>
			</table>
		</div>
	</div>
	*/

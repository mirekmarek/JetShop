<?php

/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\WarehouseManagement\Manage;

use Jet\Application_Module;
use Jet\Data_DateTime;
use JetApplication\Availabilities;
use JetApplication\DeliveryTerm;
use JetApplication\OrderDispatch;
use JetApplication\Order;
use JetApplication\Product;
use JetApplication\Product_Availability;
use JetApplication\Product_ShopData;
use JetApplication\Supplier_GoodsOrder;
use JetApplication\WarehouseManagement_LossOrDestruction;
use JetApplication\WarehouseManagement_Manager;
use JetApplication\WarehouseManagement_StockCard;
use JetApplication\WarehouseManagement_StockMovement;
use JetApplication\WarehouseManagement_StockMovement_Type;
use JetApplication\WarehouseManagement_StockVerification;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses;
use JetApplication\WarehouseManagement_Warehouse;
use JetApplication\WarehouseManagement_ReceiptOfGoods;

/**
 *
 */
class Main extends Application_Module implements WarehouseManagement_Manager
{
	public function actualizeProductsAvailability( array $product_ids ): void
	{
		foreach($product_ids as $product_id) {
			$this->actualizeProductAvailability( $product_id );
		}
	}
	
	public function actualizeProductAvailability( int $product_id ): void
	{

		foreach(Availabilities::getList() as $availability) {
			$avl_qty = 0;
			foreach($availability->getWarehouseIds() as $wh_id) {
				$wh = WarehouseManagement_Warehouse::get( $wh_id );
				$card = $wh->getCard( $product_id );
				$avl_qty += $card->getAvailable();
			}
			
			$avl = Product_Availability::get(  $availability, $product_id );
			$avl->setNumberOfAvailable( $avl_qty );
			
			$avl->save();
			
		}

	}
	
	public function actualizeOrderAvailability( Order $order ): void
	{
		$blocking = [];
		
		$wh = $order->getWarehouse();
		if(!$wh) {
			return;
		}
		
		$blocking_movement_code = WarehouseManagement_StockMovement_Type::Blocking()->getCode();
		$order_date_compare = $order->getDatePurchased()->getTimestamp();
		$oder_context = $order->getProvidesContext();
		
		$products_available = [];
		foreach($order->getPhysicalProductOverview() as $item) {
			
			$product = $item->getProduct();
			
			$wh_cad = $wh->getCard( $product->getId() );
			
			$available = $wh_cad->getInStock();
			
			$blocking = WarehouseManagement_StockMovement::getBlockingCount($wh_cad, $order->getProvidesContext(), $order->getDatePurchased());
			
			$available -= $blocking;
			
			$products_available[ $product->getId() ] = $available;
		}
		
		
		
		$avl = $order->getAvailability();
		$all_items_available = true;
		
		
		foreach($order->getItems() as $item) {
			if( !$item->isPhysicalProduct() ) {
				$item->setNumberOfUnitsAvailable( $item->getNumberOfUnits() );
				$item->setNumberOfUnitsNotAvailable( 0.0 );
				continue;
			}
			
			if( ($set_items=$item->getSetItems()) ) {

				foreach( $set_items as $set_item ) {
					$product = Product_ShopData::get( $set_item->getItemId(), $order->getShop() );
					
					if(
						!$product ||
						$product->isVirtual()
					) {
						continue;
					}
					
					$required = $set_item->getNumberOfUnits()*$item->getNumberOfUnits();
					$available = $products_available[$product->getId()];
					if($available>$required) {
						$available = $required;
					}
					
					
					$products_available[$product->getId()] -= $required;
					if($products_available[$product->getId()]<0) {
						$products_available[$product->getId()] = 0;
					}

					if( $available==$required ) {
						$set_item->setNumberOfUnitsAvailable( $required );
						$set_item->setNumberOfUnitsNotAvailable( 0.0 );
						continue;
					}
					
					if($available<=0) {
						$set_item->setNumberOfUnitsAvailable( 0.0 );
						$set_item->setNumberOfUnitsNotAvailable( $required );
					} else {
						$set_item->setNumberOfUnitsAvailable( $available );
						$set_item->setNumberOfUnitsNotAvailable( $required-$available );
					}
					
					
				}
				
				$required = $item->getNumberOfUnits();
				$available  = $required;
				
				$not_available = [];
				
				foreach( $set_items as $set_item ) {
					if($set_item->getNumberOfUnitsNotAvailable()<=0) {
						continue;
					}
					
					$not_available[] =  ceil($set_item->getNumberOfUnitsNotAvailable() / $set_item->getNumberOfUnits());
				}
				
				if($not_available) {
					$not_available = max($not_available);
					$available = $available - $not_available;
				}

			
			} else {
				$product = Product_ShopData::get( $item->getItemId(), $order->getShop() );
				if(!$product) {
					continue;
				}
				
				$required = $item->getNumberOfUnits();
				$available = $products_available[$product->getId()];
				if($available>$required) {
					$available = $required;
				}
				
				$products_available[$product->getId()] -= $required;
				if($products_available[$product->getId()]<0) {
					$products_available[$product->getId()] = 0;
				}
				
			}
			
			
			
			
			if( $available==$required ) {
				$item->setNumberOfUnitsAvailable( $required );
				$item->setNumberOfUnitsNotAvailable( 0.0 );
				continue;
			}
			
			if($available<=0) {
				$item->setNumberOfUnitsAvailable( 0.0 );
				$item->setNumberOfUnitsNotAvailable( $required );
			} else {
				$item->setNumberOfUnitsAvailable( $available );
				$item->setNumberOfUnitsNotAvailable( $required-$available );
			}
			
			$all_items_available = false;
		}

		$order->setAllItemsAvailable( $all_items_available );
		$order->save();
		
		DeliveryTerm::setupOrder( $order );
		
		$order->checkIsReady();
		
	}
	
	public function actualizeOrdersWaitingForGoods() : void
	{
		foreach( Order::getOrdersWaitingForGoods() as $order ) {
			$this->actualizeOrderAvailability( $order );
		}
	}
	
	public function manageNewOrder( Order $order ) : void {
		
		$warehouse = WarehouseManagement_Warehouse::get( $order->getShop()->getDefaultWarehouseId() );
		
		$order->setWarehouse( $warehouse );
		
		
		$product_ids = [];

		foreach( $order->getPhysicalProductOverview() as $item) {
			$product_ids[$item->getProductId()] = $item->getProductId();
			
			$warehouse->blocking(
				product_id: $item->getProductId(),
				number_of_units: $item->getNumberOfUnits(),
				context: $order->getProvidesContext()
			);
		}
		
		$this->actualizeOrderAvailability( $order );
		$this->actualizeProductsAvailability( $product_ids );
	}
	
	public function manageOrderUpdated( Order $order ) : void {
		
		$warehouse = $order->getWarehouse();
		
		$old_movements = $warehouse->cancelBlocking( $order->getProvidesContext() );
		
		$product_ids = [];
		foreach($old_movements as $movement) {
			$product_ids[$movement->getProductId()] = $movement->getProductId();
		}
		
		
		foreach( $order->getPhysicalProductOverview() as $item) {
			$product_ids[$item->getProductId()] = $item->getProductId();
			
			$warehouse->blocking(
				product_id: $item->getProductId(),
				number_of_units: $item->getNumberOfUnits(),
				context: $order->getProvidesContext()
			);
		}
		
		$this->actualizeOrderAvailability( $order );
		$this->actualizeProductsAvailability( $product_ids );
	}
	
	public function manageOrderCancelled( Order $order ) : void {
		
		$warehouse = $order->getWarehouse();
		
		$old_movements = $warehouse->cancelBlocking( $order->getProvidesContext() );
		
		$product_ids = [];
		foreach($old_movements as $movement) {
			$product_ids[$movement->getProductId()] = $movement->getProductId();
		}
		
		$this->actualizeProductsAvailability( $product_ids );
	}
	
	public function manageOrderDispatchSent( OrderDispatch $order_dispatch ) : void {
		$product_ids = [];
		$warehouse = $order_dispatch->getWarehouse();
		

		foreach($order_dispatch->getItems() as $item) {
			$product_ids[$item->getProductId()] = $item->getProductId();
			
			$warehouse->unblock(
				product_id: $item->getProductId(),
				number_of_units: $item->getNumberOfUnits(),
				context: $order_dispatch->getContext()
			);
			
			$warehouse->out(
				product_id: $item->getProductId(),
				number_of_units: $item->getNumberOfUnits(),
				context: $order_dispatch->getProvidesContext(),
			);
		}
		
		$this->actualizeProductsAvailability( $product_ids );
	}
	
	public function manageReceiptOfGoods( WarehouseManagement_ReceiptOfGoods $rcp ) : void
	{
		$warehouse = $rcp->getWarehouse();
		
		$product_ids = [];
		foreach($rcp->getItems() as $item) {
			$product_ids[$item->getProductId()] = $item->getProductId();
			
			$recalculate_price = false;
			$prev_price_per_unit = null;
			$prev_units = null;
			
			$wh_card = $warehouse->getCard( $item->getProductId() );
			
			$wh_card->reactivate();
			
			$wh_card->setSector( $item->getSector() );
			$wh_card->setRack( $item->getRack() );
			$wh_card->setPosition( $item->getPosition() );
			
			if(!$wh_card->getPricePerUnit()) {
				$wh_card->setPricePerUnit(
					price_per_unit:  $item->getPricePerUnit(),
					currency: $rcp->getCurrency()
				);
			} else {
				$recalculate_price = true;
				$prev_price_per_unit = $wh_card->getPricePerUnit();
				$prev_units = $wh_card->getInStock();
			}
			$wh_card->save();
			
			$movement = $warehouse->in(
				product_id: $item->getProductId(),
				number_of_units: $item->getUnitsReceived(),
				context: $rcp->getProvidesContext(),
				currency: $rcp->getCurrency(),
				price_per_unit: $item->getPricePerUnit(),
				sector: $item->getSector(),
				rack: $item->getRack(),
				position: $item->getPosition()
			);
			
			if($recalculate_price) {
				$new_price_per_unit = ($prev_price_per_unit*$prev_units) + ($movement->getPricePerUnit()*$movement->getNumberOfUnits());
				$new_price_per_unit = $new_price_per_unit / ( $prev_units + $movement->getNumberOfUnits() );
				
				$wh_card->setPricePerUnit( $new_price_per_unit );
				$wh_card->save();
			}
		}

		if($rcp->getOrderId()) {
			$order = Supplier_GoodsOrder::load( $rcp->getOrderId() );
			$order?->received( $rcp );
		}
		
		$this->actualizeProductsAvailability( $product_ids );
		$this->actualizeOrdersWaitingForGoods();
	}
	
	public function manageTransferBetweenWarehousesSent( WarehouseManagement_TransferBetweenWarehouses $transfer ) : void
	{
		$warehouse = $transfer->getSourceWarehouse();
		
		$product_ids = [];
		foreach($transfer->getItems() as $item) {
			
			$warehouse->transferOut(
				product_id: $item->getProductId(),
				number_of_units: $item->getNumberOfUnits(),
				context: $transfer->getProvidesContext(),
			);
			
		}
		$this->actualizeProductsAvailability( $product_ids );
		$this->actualizeOrdersWaitingForGoods();
	}
	
	public function manageTransferBetweenWarehousesReceived( WarehouseManagement_TransferBetweenWarehouses $transfer ) : void
	{
		$warehouse = $transfer->getTargetWarehouse();
		
		$product_ids = [];
		foreach($transfer->getItems() as $item) {
			$recalculate_price = false;
			$prev_price_per_unit = null;
			$prev_units = null;
			
			
			$wh_card = $warehouse->getCard( $item->getProductId() );
			$wh_card->reactivate();
			
			$wh_card->setSector( $item->getTargetSector() );
			$wh_card->setRack( $item->getTargetRack() );
			$wh_card->setPosition( $item->getTargetPosition() );
			
			if(!$wh_card->getPricePerUnit()) {
				$wh_card->setPricePerUnit(
					price_per_unit:  $item->getPricePerUnit(),
					currency: $item->getCurrency()
				);
			} else {
				$recalculate_price = true;
				$prev_price_per_unit = $wh_card->getPricePerUnit();
				$prev_units = $wh_card->getInStock();
			}
			
			
			$wh_card->save();
			
			
			$movement = $warehouse->transferIn(
				product_id: $item->getProductId(),
				number_of_units: $item->getNumberOfUnits(),
				context: $transfer->getProvidesContext(),
				currency: $item->getCurrency(),
				price_per_unit: $item->getPricePerUnit(),
				sector: $item->getTargetSector(),
				rack: $item->getTargetRack(),
				position: $item->getTargetPosition()
			);
			
			if($recalculate_price) {
				$new_price_per_unit = ($prev_price_per_unit*$prev_units) + ($movement->getPricePerUnit()*$movement->getNumberOfUnits());
				$new_price_per_unit = $new_price_per_unit / ( $prev_units + $movement->getNumberOfUnits() );
				
				$wh_card->setPricePerUnit( $new_price_per_unit );
				$wh_card->save();
			}
			
			
		}
		$this->actualizeProductsAvailability( $product_ids );
		$this->actualizeOrdersWaitingForGoods();
	}
	
	public function manageTransferBetweenWarehousesCanceled( WarehouseManagement_TransferBetweenWarehouses $transfer ) : void
	{
		if($transfer->getStatus()!=WarehouseManagement_TransferBetweenWarehouses::STATUS_SENT) {
			return;
		}
		
		$warehouse = $transfer->getSourceWarehouse();
		
		$product_ids = [];
		foreach($transfer->getItems() as $item) {
			$warehouse->cancelMovement(
				product_id: $item->getProductId(),
				context: $transfer->getProvidesContext(),
				type: WarehouseManagement_StockMovement_Type::TransferOut()
			);
		}
		$this->actualizeProductsAvailability( $product_ids );
	}
	
	
	public function manageStockVerification( WarehouseManagement_StockVerification $verification ) : void
	{
		foreach($verification->getItems() as $item) {
			$diff = $item->getNumberOfUnitsReality() - $item->getNumberOfUnitsExpected();
			if($diff>=0) {
				continue;
			}
			
			$diff = abs($diff);
			
			$loss = new WarehouseManagement_LossOrDestruction();
			$loss->setDate( Data_DateTime::now() );
			$loss->setWarehouseId( $verification->getWarehouseId() );
			$loss->setProduct( $item->getProductId() );
			$loss->save();
			
		}
	}
	
	public function howManyItemsMustBeOrdered( Product $product ) : float
	{
		$cards = WarehouseManagement_StockCard::getCardsByProduct( $product->getId() );
		
		$res = 0.0;
		
		foreach($cards as $card) {
			if($card->getAvailable()<0) {
				$res += -1*$card->getAvailable();
			}
		}
		
		return $res;
	}
	
	public function howManyItemsShouldBeOrdered( Product $product ) : float
	{
		//TODO:
		return 0.0;
	}
	
	public function manageLossOrDestruction( WarehouseManagement_LossOrDestruction $loss_or_destruction ) : void
	{
		$warehouse = $loss_or_destruction->getWarehouse();
		
		$warehouse->movement(
			type: WarehouseManagement_StockMovement_Type::LossOrDestruction(),
			product_id: $loss_or_destruction->getProductId(),
			number_of_units: $loss_or_destruction->getNumberOfUnits(),
			context: $loss_or_destruction->getProvidesContext(),
			currency: $loss_or_destruction->getCurrency(),
			price_per_unit: $loss_or_destruction->getPricePerUnit()
		);
		
		$this->actualizeProductsAvailability( [$loss_or_destruction->getProductId()] );
	}
	
}



<?php
namespace JetShop;

use JetApplication\Order;
use JetApplication\OrderDispatch;
use JetApplication\Product;
use JetApplication\WarehouseManagement_LossOrDestruction;
use JetApplication\WarehouseManagement_ReceiptOfGoods;
use JetApplication\WarehouseManagement_StockVerification;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses;

interface Core_WarehouseManagement_Manager {
	public function manageNewOrder( Order $order ) : void;
	
	public function manageOrderUpdated( Order $order ) : void;
	
	public function manageOrderCancelled( Order $order ) : void;
	
	public function manageOrderDispatchSent( OrderDispatch $order_dispatch ) : void;
	
	public function manageReceiptOfGoods( WarehouseManagement_ReceiptOfGoods $rcp ) : void;
	
	public function manageTransferBetweenWarehousesSent( WarehouseManagement_TransferBetweenWarehouses $transfer ) : void;
	
	public function manageTransferBetweenWarehousesReceived( WarehouseManagement_TransferBetweenWarehouses $transfer ) : void;
	
	public function manageTransferBetweenWarehousesCanceled( WarehouseManagement_TransferBetweenWarehouses $transfer ) : void;
	
	public function manageStockVerification( WarehouseManagement_StockVerification $verification ) : void;
	
	public function manageLossOrDestruction( WarehouseManagement_LossOrDestruction $loss_or_destruction ) : void;
	
	public function howManyItemsMustBeOrdered( Product $product ) : float;
	
	public function howManyItemsShouldBeOrdered( Product $product ) : float;
	
	public function actualizeProductsAvailability( array $product_ids ): void;
	
	public function actualizeProductAvailability( int $product_id ): void;
	
	public function actualizeOrderAvailability( Order $order ): void;
	
	public function actualizeOrdersWaitingForGoods() : void;
	
	
}
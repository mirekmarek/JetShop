<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Application_Module;
use JetApplication\Manager_MetaInfo;
use JetApplication\Order;
use JetApplication\OrderDispatch;
use JetApplication\OrderPersonalReceipt;
use JetApplication\Product;
use JetApplication\WarehouseManagement_LossOrDestruction;
use JetApplication\WarehouseManagement_ReceiptOfGoods;
use JetApplication\WarehouseManagement_StockVerification;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_GENERAL,
	is_mandatory: false,
	name: 'Warehouse management',
	description: '',
	module_name_prefix: ''
)]
abstract class Core_WarehouseManagement_Manager extends Application_Module
{
	abstract public function manageNewOrder( Order $order ) : void;
	
	abstract public function manageOrderUpdated( Order $order ) : void;
	
	abstract public function manageOrderCancelled( Order $order ) : void;
	
	abstract public function manageOrderDispatchSent( OrderDispatch $order_dispatch ) : void;
	
	abstract public function manageOrderPersonalReceiptHandedOver( OrderPersonalReceipt $order_personal_receipt ) : void;
	
	abstract public function manageReceiptOfGoods( WarehouseManagement_ReceiptOfGoods $rcp ) : void;
	
	abstract public function manageTransferBetweenWarehousesSent( WarehouseManagement_TransferBetweenWarehouses $transfer ) : void;
	
	abstract public function manageTransferBetweenWarehousesReceived( WarehouseManagement_TransferBetweenWarehouses $transfer ) : void;
	
	abstract public function manageTransferBetweenWarehousesCanceled( WarehouseManagement_TransferBetweenWarehouses $transfer ) : void;
	
	abstract public function manageStockVerification( WarehouseManagement_StockVerification $verification ) : void;
	
	abstract public function manageLossOrDestruction( WarehouseManagement_LossOrDestruction $loss_or_destruction ) : void;
	
	abstract public function howManyItemsMustBeOrdered( Product $product ) : float;
	
	abstract public function howManyItemsShouldBeOrdered( Product $product ) : float;
	
	abstract public function actualizeProductsAvailability( array $product_ids ): void;
	
	abstract public function actualizeProductAvailability( int $product_id ): void;
	
	abstract public function actualizeOrderAvailability( Order $order ): void;
	
	abstract public function actualizeOrdersWaitingForGoods() : void;
	
}
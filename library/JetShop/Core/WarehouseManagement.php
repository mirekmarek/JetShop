<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Application_Service_General;
use JetApplication\OrderDispatch;
use JetApplication\Order;
use JetApplication\OrderPersonalReceipt;
use JetApplication\Product;
use JetApplication\WarehouseManagement;
use JetApplication\WarehouseManagement_LossOrDestruction;
use JetApplication\Application_Service_General_WarehouseManagement;
use JetApplication\WarehouseManagement_ReceiptOfGoods;
use JetApplication\WarehouseManagement_StockVerification;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses;

abstract class Core_WarehouseManagement {

	public static function getManager() : ?Application_Service_General_WarehouseManagement
	{
		return Application_Service_General::WarehouseManagement();
	}
	
	public static function manageNewOrder( Order $order ) : void {
		WarehouseManagement::getManager()?->manageNewOrder( $order );
	}
	
	public static function manageOrderUpdated( Order $order ) : void {
		WarehouseManagement::getManager()?->manageOrderUpdated( $order );
	}
	
	public static function manageCancelledOrder( Order $order ) : void {
		WarehouseManagement::getManager()?->manageOrderCancelled( $order );
	}
	
	public static  function manageOrderCancelled( Order $order ) : void
	{
		WarehouseManagement::getManager()?->manageOrderCancelled( $order );
	}
	
	public static function manageOrderDispatchSent( OrderDispatch $order_dispatch ) : void
	{
		WarehouseManagement::getManager()?->manageOrderDispatchSent( $order_dispatch );
	}
	
	
	public static function manageOrderPersonalReceiptHandedOver( OrderPersonalReceipt $order_personal_receipt ) : void
	{
		WarehouseManagement::getManager()?->manageOrderPersonalReceiptHandedOver( $order_personal_receipt );
	}
	
	
	public static function manageReceiptOfGoods( WarehouseManagement_ReceiptOfGoods $rcp ) : void
	{
		WarehouseManagement::getManager()?->manageReceiptOfGoods( $rcp );
	}
	
	public static function manageTransferBetweenWarehousesSent( WarehouseManagement_TransferBetweenWarehouses $transfer ) : void
	{
		WarehouseManagement::getManager()?->manageTransferBetweenWarehousesSent( $transfer );
	}
	
	public static function manageTransferBetweenWarehousesReceived( WarehouseManagement_TransferBetweenWarehouses $transfer ) : void
	{
		WarehouseManagement::getManager()?->manageTransferBetweenWarehousesReceived( $transfer );
	}
	
	public static function manageTransferBetweenWarehousesCanceled( WarehouseManagement_TransferBetweenWarehouses $transfer ) : void
	{
		WarehouseManagement::getManager()?->manageTransferBetweenWarehousesCanceled( $transfer );
	}
	
	public static function manageStockVerification( WarehouseManagement_StockVerification $verification ) : void
	{
		WarehouseManagement::getManager()?->manageStockVerification( $verification );
	}
	
	public static function manageLossOrDestruction( WarehouseManagement_LossOrDestruction $loss_or_destruction ) : void
	{
		WarehouseManagement::getManager()?->manageLossOrDestruction( $loss_or_destruction );
	}
	
	public static function howManyItemsMustBeOrdered( Product $product ) : float
	{
		return WarehouseManagement::getManager()?->howManyItemsMustBeOrdered( $product ) ?? 0.0;
	}
	
	public static function howManyItemsShouldBeOrdered( Product $product ) : float
	{
		return WarehouseManagement::getManager()?->howManyItemsShouldBeOrdered( $product ) ?? 0.0;
	}
	
	public static function actualizeProductsAvailability( array $product_ids ): void
	{
		WarehouseManagement::getManager()?->actualizeProductsAvailability( $product_ids );
	}
	
	public static function actualizeProductAvailability( int $product_id ): void
	{
		WarehouseManagement::getManager()?->actualizeProductAvailability( $product_id );
	}
	
	public static function actualizeOrderAvailability( Order $order ): void
	{
		WarehouseManagement::getManager()?->actualizeOrderAvailability( $order );
	}
	
	public static function actualizeOrdersWaitingForGoods() : void
	{
		WarehouseManagement::getManager()?->actualizeOrdersWaitingForGoods();
	}
	
	
}
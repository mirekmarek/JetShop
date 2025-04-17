<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\SupplierGoodsOrder\GoodsReceived;

use JetApplication\Supplier_GoodsOrder_Event_HandlerModule;
use JetApplication\WarehouseManagement_ReceiptOfGoods;

class Main extends Supplier_GoodsOrder_Event_HandlerModule
{

	public function handleInternals(): bool
	{
		$rcp = WarehouseManagement_ReceiptOfGoods::get( $this->event->getContextObjectId() );
		if(!$rcp) {
			return false;
		}
		
		foreach($rcp->getItems() as $rcp_item) {
			$order_item = $this->order->getItems()[$rcp_item->getProductId()] ?? null;
			if(!$order_item) {
				continue;
			}
			
			//$order_item->setUnitsReceived( $order_item->getUnitsReceived() + $rcp_item->getUnitsReceived() );
			$order_item->setUnitsReceived( $rcp_item->getUnitsReceived() );
			$order_item->save();
		}
		
		$this->order->setGoodsReceivedDate( $rcp->getReceiptDate() );
		$this->order->save();
		
		return true;
	}
	
	public function getEventNameReadable(): string
	{
		return 'Goods received';
	}
	
	public function getEventCSSClass(): string
	{
		return 'event-done-success';
	}
}
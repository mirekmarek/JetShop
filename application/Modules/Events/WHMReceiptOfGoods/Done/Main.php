<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\WHMReceiptOfGoods\Done;

use JetApplication\Supplier_GoodsOrder;
use JetApplication\WarehouseManagement;
use JetApplication\WarehouseManagement_ReceiptOfGoods_Event_HandlerModule;

class Main extends WarehouseManagement_ReceiptOfGoods_Event_HandlerModule
{
	
	public function handleInternals(): bool
	{
		$this->rcp->cleanupItems();
		
		$order = Supplier_GoodsOrder::get( $this->rcp->getOrderId() );
		$order?->received( $this->rcp );
		
		WarehouseManagement::manageReceiptOfGoods( $this->rcp );
		
		return true;
	}
	
	public function getEventNameReadable(): string
	{
		return 'Done';
	}
	
	public function getEventCSSClass(): string
	{
		return 'event-done-success';
	}
}
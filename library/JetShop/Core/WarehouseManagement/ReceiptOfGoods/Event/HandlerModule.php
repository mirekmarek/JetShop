<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\EShopEntity_Event;
use JetApplication\Event_HandlerModule;
use JetApplication\WarehouseManagement_ReceiptOfGoods;
use JetApplication\WarehouseManagement_ReceiptOfGoods_Event;


abstract class Core_WarehouseManagement_ReceiptOfGoods_Event_HandlerModule extends Event_HandlerModule
{
	protected WarehouseManagement_ReceiptOfGoods_Event $event;
	protected WarehouseManagement_ReceiptOfGoods $rcp;
	
	
	public function init( EShopEntity_Event|WarehouseManagement_ReceiptOfGoods_Event $event ) : void
	{
		$this->event = $event;
		$this->rcp = $event->getRcp();
	}
	
	public function getEvent(): WarehouseManagement_ReceiptOfGoods_Event
	{
		return $this->event;
	}
	
	public function getRcp(): WarehouseManagement_ReceiptOfGoods
	{
		return $this->rcp;
	}
	
	
	public function handleExternals(): bool
	{
		return true;
	}
	
	
	public function sendNotifications(): bool
	{
		return true;
	}
	
}
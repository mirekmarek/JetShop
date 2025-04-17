<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\EShopEntity_Event;
use JetApplication\Event_HandlerModule;
use JetApplication\Supplier_GoodsOrder;
use JetApplication\Supplier_GoodsOrder_Event;


abstract class Core_Supplier_GoodsOrder_Event_HandlerModule extends Event_HandlerModule
{
	protected Supplier_GoodsOrder_Event $event;
	protected Supplier_GoodsOrder $order;
	
	
	public function init( EShopEntity_Event|Supplier_GoodsOrder_Event $event ) : void
	{
		$this->event = $event;
		$order = $event->getOrder();
		$this->order = $order;
	}
	
	public function getEvent(): Supplier_GoodsOrder_Event
	{
		return $this->event;
	}
	
	public function getOrder(): Supplier_GoodsOrder
	{
		return $this->order;
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
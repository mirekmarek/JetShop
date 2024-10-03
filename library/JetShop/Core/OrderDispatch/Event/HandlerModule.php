<?php
/**
 *
 */
namespace JetShop;

use JetApplication\Entity_Event;
use JetApplication\Event_HandlerModule;
use JetApplication\OrderDispatch;
use JetApplication\OrderDispatch_Event;
use JetApplication\Shops_Shop;


abstract class Core_OrderDispatch_Event_HandlerModule extends Event_HandlerModule
{
	protected OrderDispatch_Event $event;
	protected Shops_Shop $shop;
	protected OrderDispatch $order_dispatch;


	public function init( Entity_Event $event ) : void
	{
		/** @noinspection PhpFieldAssignmentTypeMismatchInspection */
		$this->event = $event;
		$order_dispatch = $event->getOrderDispatch();
		$this->shop = $order_dispatch->getShop();
		$this->order_dispatch = $order_dispatch;
	}

	public function getEvent(): OrderDispatch_Event
	{
		return $this->event;
	}

	public function getOrderDispatch(): OrderDispatch
	{
		return $this->order_dispatch;
	}
	
}
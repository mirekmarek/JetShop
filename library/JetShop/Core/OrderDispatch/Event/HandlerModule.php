<?php
/**
 *
 */
namespace JetShop;

use JetApplication\EShopEntity_Event;
use JetApplication\Event_HandlerModule;
use JetApplication\OrderDispatch;
use JetApplication\OrderDispatch_Event;
use JetApplication\EShop;


abstract class Core_OrderDispatch_Event_HandlerModule extends Event_HandlerModule
{
	protected OrderDispatch_Event $event;
	protected EShop $eshop;
	protected OrderDispatch $order_dispatch;


	public function init( EShopEntity_Event $event ) : void
	{
		/** @noinspection PhpFieldAssignmentTypeMismatchInspection */
		$this->event = $event;
		$order_dispatch = $event->getOrderDispatch();
		$this->eshop = $order_dispatch->getEshop();
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
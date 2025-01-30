<?php
/**
 *
 */
namespace JetShop;

use JetApplication\EShopEntity_Event;
use JetApplication\Event_HandlerModule;
use JetApplication\OrderPersonalReceipt;
use JetApplication\OrderPersonalReceipt_Event;
use JetApplication\EShop;


abstract class Core_OrderPersonalReceipt_Event_HandlerModule extends Event_HandlerModule
{
	protected OrderPersonalReceipt_Event $event;
	protected EShop $eshop;
	protected OrderPersonalReceipt $order_personal_receipt;
	
	
	public function init( EShopEntity_Event $event ) : void
	{
		/** @noinspection PhpFieldAssignmentTypeMismatchInspection */
		$this->event = $event;
		$order_dispatch = $event->getOrderPersonalReceipt();
		$this->eshop = $order_dispatch->getEshop();
		$this->order_personal_receipt = $order_dispatch;
	}
	
	public function getEvent(): OrderPersonalReceipt_Event
	{
		return $this->event;
	}
	
	public function getOrderPersonalReceipt(): OrderPersonalReceipt
	{
		return $this->order_personal_receipt;
	}
	
}
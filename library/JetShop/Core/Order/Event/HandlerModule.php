<?php
/**
 *
 */

namespace JetShop;


use JetApplication\Entity_Event;
use JetApplication\Event_HandlerModule;
use JetApplication\Order_Event;
use JetApplication\Shops_Shop;
use JetApplication\Order;


abstract class Core_Order_Event_HandlerModule extends Event_HandlerModule
{
	protected Order_Event $event;
	protected Shops_Shop $shop;
	protected Order $order;


	public function init( Entity_Event $event ) : void
	{
		/** @noinspection PhpFieldAssignmentTypeMismatchInspection */
		$this->event = $event;
		$order = $event->getOrder();
		$this->shop = $order->getShop();
		$this->order = $order;
	}

	public function getEvent(): Order_Event
	{
		return $this->event;
	}

	public function getOrder(): Order
	{
		return $this->order;
	}

	
	public function sendEMail( Core_Order_EMailTemplate $template ) : bool
	{
		$template->setEvent($this->event);
		$email = $template->createEmail( $this->getEvent()->getShop() );
		
		return $email->send();
	}
}
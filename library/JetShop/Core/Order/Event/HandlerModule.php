<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;



use JetApplication\EShopEntity_Event;
use JetApplication\Event_HandlerModule;
use JetApplication\Order_Event;
use JetApplication\EShop;
use JetApplication\Order;
use JetApplication\Order_EMailTemplate;


abstract class Core_Order_Event_HandlerModule extends Event_HandlerModule
{
	protected Order_Event $event;
	protected EShop $eshop;
	protected Order $order;


	public function init( EShopEntity_Event|Order_Event $event ) : void
	{
		$this->event = $event;
		$order = $event->getOrder();
		$this->eshop = $order->getEshop();
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

	
	public function sendEMail( Order_EMailTemplate $template ) : bool
	{
		$template->setEvent($this->event);
		$email = $template->createEmail( $this->getEvent()->getEshop() );
		
		return $email?->send()??true;
	}
}
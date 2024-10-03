<?php
/**
 *
 */
namespace JetShop;


use JetApplication\Entity_Event;
use JetApplication\Event_HandlerModule;
use JetApplication\ReturnOfGoods_Event;
use JetApplication\Shops_Shop;
use JetApplication\ReturnOfGoods;


abstract class Core_ReturnOfGoods_Event_HandlerModule extends Event_HandlerModule
{
	protected ReturnOfGoods_Event $event;
	protected Shops_Shop $shop;
	protected ReturnOfGoods $return_of_goods;


	public function init( Entity_Event $event ) : void
	{
		/** @noinspection PhpFieldAssignmentTypeMismatchInspection */
		$this->event = $event;
		$return = $event->getReturnOfGoods();
		$this->shop = $return->getShop();
		$this->return_of_goods = $return;
	}

	public function getEvent(): ReturnOfGoods_Event
	{
		return $this->event;
	}

	public function getReturnOfGoods(): ReturnOfGoods
	{
		return $this->return_of_goods;
	}

}
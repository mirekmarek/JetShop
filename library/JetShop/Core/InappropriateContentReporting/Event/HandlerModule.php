<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\EShopEntity_Event;
use JetApplication\Event_HandlerModule;
use JetApplication\EShop;
use JetApplication\InappropriateContentReporting;
use JetApplication\InappropriateContentReporting_Event;


abstract class Core_InappropriateContentReporting_Event_HandlerModule extends Event_HandlerModule
{
	protected InappropriateContentReporting_Event $event;
	protected EShop $eshop;
	protected InappropriateContentReporting $review;
	
	
	public function init( EShopEntity_Event|InappropriateContentReporting_Event $event ) : void
	{
		$this->event = $event;
		$this->eshop = $event->getInappropriateContentReporting()->getEShop();
		$this->review = $event->getInappropriateContentReporting();
	}
	
	public function getEvent(): InappropriateContentReporting_Event
	{
		return $this->event;
	}
	
	public function getReview(): InappropriateContentReporting
	{
		return $this->review;
	}

}
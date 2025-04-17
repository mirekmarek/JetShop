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
use JetApplication\ProductQuestion;
use JetApplication\ProductQuestion_Event;


abstract class Core_ProductQuestion_Event_HandlerModule extends Event_HandlerModule
{
	protected ProductQuestion_Event $event;
	protected EShop $eshop;
	protected ProductQuestion $question;
	
	
	public function init( EShopEntity_Event|ProductQuestion_Event $event ) : void
	{
		$this->event = $event;
		$this->eshop = $event->getProductQuestion()->getEShop();
		$this->question = $event->getProductQuestion();
	}
	
	public function getEvent(): ProductQuestion_Event
	{
		return $this->event;
	}
	
	public function getQuestion(): ProductQuestion
	{
		return $this->question;
	}

}
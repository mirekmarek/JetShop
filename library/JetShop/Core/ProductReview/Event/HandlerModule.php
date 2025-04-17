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
use JetApplication\ProductReview;
use JetApplication\ProductReview_Event;


abstract class Core_ProductReview_Event_HandlerModule extends Event_HandlerModule
{
	protected ProductReview_Event $event;
	protected EShop $eshop;
	protected ProductReview $review;
	
	
	public function init( EShopEntity_Event|ProductReview_Event $event ) : void
	{
		$this->event = $event;
		$this->eshop = $event->getProductReview()->getEShop();
		$this->review = $event->getProductReview();
	}
	
	public function getEvent(): ProductReview_Event
	{
		return $this->event;
	}
	
	public function getReview(): ProductReview
	{
		return $this->review;
	}

}
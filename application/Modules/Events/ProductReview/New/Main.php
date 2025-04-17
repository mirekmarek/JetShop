<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\ProductReview\New;

use JetApplication\ProductReview_Event_HandlerModule;


class Main extends ProductReview_Event_HandlerModule
{
	public function handleExternals(): bool
	{
		return true;
	}
	
	public function handleInternals(): bool
	{
		
		$updated = false;
		if($this->review->isAssessed()) {
			$this->review->setAssessed( false );
			$this->review->setAssessedDateTime( null );
			$updated = true;
		}
		if($this->review->isApproved()) {
			$this->review->setApproved( false );
			$this->review->setApprovedDateTime( null );
			$updated = true;
		}
		
		if($updated) {
			$this->review->save();
			$this->review->actualizeProduct();
		}
		
		return true;
	}
	
	public function sendNotifications(): bool
	{
		return true;
	}
	
	public function getEventNameReadable(): string
	{
		return 'New';
	}
	
	public function getEventCSSClass(): string
	{
		return 'event-new';
	}
}
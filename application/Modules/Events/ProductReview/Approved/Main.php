<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\ProductReview\Approved;


use Jet\Data_DateTime;
use JetApplication\ProductReview_Event_HandlerModule;


class Main extends ProductReview_Event_HandlerModule
{
	public function handleExternals(): bool
	{
		return true;
	}
	
	public function handleInternals(): bool
	{
		$this->review->setAssessed( true );
		$this->review->setAssessedDateTime( Data_DateTime::now() );
		$this->review->setApproved( true );
		$this->review->setApprovedDateTime( Data_DateTime::now() );
		$this->review->save();
		
		$this->review->actualizeProduct();
		
		return true;
	}
	
	public function sendNotifications(): bool
	{
		return true;
	}
	
	public function getEventNameReadable(): string
	{
		return 'Approved';
	}
	
	public function getEventCSSClass(): string
	{
		return 'event-done-success';
	}
}
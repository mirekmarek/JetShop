<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\ProductQuestion\Rejected;

use JetApplication\ProductQuestion_Event_HandlerModule;


class Main extends ProductQuestion_Event_HandlerModule
{
	public function handleExternals(): bool
	{
		return true;
	}
	
	public function handleInternals(): bool
	{
		$q = $this->question;
		
		$q->setAnswered( false );
		$q->setAnsweredDateTime( null );
		$q->setDisplay( false );
		$q->save();
		$q->actualizeProduct();
		
		return true;
	}
	
	public function sendNotifications(): bool
	{
		return true;
	}
	
	public function getEventNameReadable(): string
	{
		return 'Rejected';
	}
	
	public function getEventCSSClass(): string
	{
		return 'event-rejected';
	}
}
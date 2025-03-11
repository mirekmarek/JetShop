<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\Complaint\Returned;


use JetApplication\Complaint_Event_HandlerModule;


class Main extends Complaint_Event_HandlerModule
{
	
	
	public function sendNotifications(): bool
	{
		return true;
	}
	
	public function handleExternals(): bool
	{
		return true;
	}
	
	public function handleInternals(): bool
	{
		return true;
	}
	
	public function getEventNameReadable(): string
	{
		return 'Returned';
	}
	
	public function getEventCSSClass(): string
	{
		return 'event-rejected';
	}
}
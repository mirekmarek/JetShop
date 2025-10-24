<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\Complaint\Accepted;


use JetApplication\Complaint_Event_HandlerModule;
use JetApplication\EMail_TemplateProvider;


class Main extends Complaint_Event_HandlerModule implements EMail_TemplateProvider
{
	
	
	public function sendNotifications(): bool
	{
		return true;
	}
	
	public function getEMailTemplates(): array
	{
		return [];
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
		return 'Accepted - Done';
	}
	
	public function getEventCSSClass(): string
	{
		return 'event-done-success';
	}
}
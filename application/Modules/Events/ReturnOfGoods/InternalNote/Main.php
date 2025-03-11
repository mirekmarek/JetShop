<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\ReturnOfGoods\InternalNote;


use JetApplication\EMail_TemplateProvider;
use JetApplication\ReturnOfGoods_Event_HandlerModule;


class Main extends ReturnOfGoods_Event_HandlerModule implements EMail_TemplateProvider
{
	
	public function handleExternals(): bool
	{
		return true;
	}

	public function handleInternals(): bool
	{
			return true;
	}
	
	public function sendNotifications(): bool
	{
		return true;
	}
	
	
	public function getEMailTemplates(): array
	{
		return [];
	}
	
	public function getEventNameReadable(): string
	{
		return 'Internal note';
	}
	
	public function getEventCSSClass(): string
	{
		return 'event-internal-note';
	}
}
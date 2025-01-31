<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\Order\InternalNote;


use JetApplication\EMail_TemplateProvider;
use JetApplication\MarketplaceIntegration;
use JetApplication\Order_Event_HandlerModule;


class Main extends Order_Event_HandlerModule implements EMail_TemplateProvider
{
	
	public function handleExternals(): bool
	{
		$res = MarketplaceIntegration::handleOrderEvent( $this->event );
		if($res!==null) {
			return $res;
		}
		
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
	
	public function getEventStyle(): string
	{
		return 'background: #b9b9b9';
	}
}
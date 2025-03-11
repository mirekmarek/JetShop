<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\Order\NotReadyForDispatch;


use JetApplication\MarketplaceIntegration;
use JetApplication\Order_Event_HandlerModule;


class Main extends Order_Event_HandlerModule
{
	
	public function sendNotifications(): bool
	{
		//TODO:
		return true;
	}
	
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
	
	public function getEventNameReadable(): string
	{
		return 'Order is not ready for dispatch';
	}
	
	public function getEventCSSClass(): string
	{
		return 'event-not-ready';
	}
}
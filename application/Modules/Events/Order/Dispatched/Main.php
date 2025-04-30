<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\Order\Dispatched;


use JetApplication\EMail_TemplateProvider;
use JetApplication\MarketplaceIntegration;
use JetApplication\Order_Event_HandlerModule;
use JetApplication\OrderDispatch;


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
		
		$dispatch = $this->event->getContext();
		
		$template = new EMailTemplate();
		
		if($dispatch instanceof OrderDispatch ) {
			$template->setCarrierName( $dispatch->getCarrier()?->getName()??$dispatch->getCarrierCode() );
			$template->setTrackingUrl( $dispatch->getTrackingURL() );
			$template->setConsignmentNumber( $dispatch->getTrackingNumber() );
		}
		
		$email = $template->createEmail( $this->getEvent()->getEshop() );
		
		return $email?->send()??true;
	}
	
	
	public function getEMailTemplates(): array
	{
		$template = new EMailTemplate();
		
		return [$template];
	}
	
	public function getEventNameReadable(): string
	{
		return 'Order dispatched';
	}
	
	public function getEventCSSClass(): string
	{
		return 'event-dispatched';
	}
}
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
use JetApplication\Order_Status_Dispatched;
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
		$this->order->setStatus(
			Order_Status_Dispatched::get(),
			handle_event: false
		);
		
		return true;
	}
	
	public function sendNotifications(): bool
	{
		
		$dispatch = $this->event->getContext();
		
		$template = new EMailTemplate();
		$template->setOrder( $this->event->getOrder() );
		
		if($dispatch instanceof OrderDispatch ) {
			$template->setCarrierName( $dispatch->getCarrierCode() );
			$template->setTrackingUrl( $dispatch->getOurNote() );
			$template->setConsignmentNumber( $dispatch->getTrackingNumber() );
			
			$note =
			"Dopravce: {$dispatch->getCarrierCode()}<br>".
			"Číslo zásilky: <b>{$dispatch->getTrackingNumber()}</b><br>".
			"URL pro sledování: <a href=\"{$dispatch->getOurNote()}\" target=\"_blank\">{$dispatch->getOurNote()}</a><br>";
			
			$this->event->setInternalNote($note);
			
			$this->event::updateData(
				data: [
					'internal_note' => $note
				],
				where: [
					'id' => $this->event->getId()
				]
			);
			
		}
		
		$email = $template->createEmail( $this->getEvent()->getEshop() );
		
		
		$email?->send();
		
		return true;
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
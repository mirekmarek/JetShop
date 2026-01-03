<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\Order\PersonalReceiptPrepared;


use JetApplication\EMail_TemplateProvider;
use JetApplication\MarketplaceIntegration;
use JetApplication\Order_Event_HandlerModule;
use JetApplication\SMS_TemplateProvider;


class Main extends Order_Event_HandlerModule implements EMail_TemplateProvider, SMS_TemplateProvider
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
		$this->sendEMail( new EMailTemplate() );
		
		$template = new SMSTemplate();
		$template->setEvent( $this->event );
		$template->createSMS( $this->getEvent()->getEshop() )?->send();
		
		return true;
	}
	
	
	public function getEMailTemplates(): array
	{
		$template = new EMailTemplate();
		
		return [$template];
	}
	
	public function getEventNameReadable(): string
	{
		return 'Personal Receipt Prepared';
	}
	
	public function getEventCSSClass(): string
	{
		return 'event-dispatched';
	}
	
	public function getSMSTemplates(): array
	{
		return [
			new SMSTemplate()
		];
	}
}
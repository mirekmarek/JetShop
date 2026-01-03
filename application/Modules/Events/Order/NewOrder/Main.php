<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\Order\NewOrder;


use JetApplication\Application_Service_EShop;
use JetApplication\EMail_TemplateProvider;
use JetApplication\MarketplaceIntegration;
use JetApplication\Order;
use JetApplication\Order_Event_HandlerModule;
use JetApplication\SMS_TemplateProvider;
use JetApplication\WarehouseManagement;


class Main extends Order_Event_HandlerModule implements EMail_TemplateProvider, SMS_TemplateProvider
{
	
	public function handleExternals(): bool
	{
		foreach( Application_Service_EShop::NewOrderPostprocessors( $this->event->getEshop() ) as $postprocessor ) {
			$postprocessor->processNewOrder( $this->event->getOrder() );
		}
		
		$res = MarketplaceIntegration::handleOrderEvent( $this->event );
		if($res!==null) {
			return $res;
		}
		
		return true;
	}

	public function handleInternals(): bool
	{
		
		WarehouseManagement::manageNewOrder( $this->order );
		
		$this->order->checkIsReady();

		return true;
	}
	
	public function sendNewOrderEmail( Order $order ) : bool
	{
		$template = new EMailTemplate();
		$template->setOrder( $order );
		$email = $template->createEmail( $order->getEshop() );
		
		return $email?->send()??true;
	}
	
	
	public function sendNotifications(): bool
	{
		$template = new EMailTemplate();
		$template->setEvent($this->event);
		$email = $template->createEmail( $this->getEvent()->getEshop() );
		if($email) {
			$this->order->getPaymentMethod()->updateOrderConfirmationEmail( $this->order, $email );
			$email->send();
		}
		
		$template = new SMSTemplate();
		$template->setEvent($this->event);
		$template->createSMS( $this->getEvent()->getEshop() )?->send();
		
		return true;
	}
	
	public function getEMailTemplates(): array
	{
		$template = new EMailTemplate();
		
		return [$template];
	}
	
	
	public function getSMSTemplates(): array
	{
		$template = new SMSTemplate();
		
		return [$template];
	}
	
	public function getEventNameReadable(): string
	{
		return 'Order created';
	}
	
	public function getEventCSSClass(): string
	{
		return 'event-new';
	}
	
}
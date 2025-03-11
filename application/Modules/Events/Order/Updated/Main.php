<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\Order\Updated;


use JetApplication\EMail_TemplateProvider;
use JetApplication\MarketplaceIntegration;
use JetApplication\Order;
use JetApplication\Order_Event_HandlerModule;
use JetApplication\WarehouseManagement;


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
		WarehouseManagement::manageOrderUpdated( $this->order );
		
		$this->order->checkIsReady();
		
		return true;
	}
	
	public function sendOrderUpdatedEmail( Order $order ) : bool
	{
		$template = new EMailTemplate();
		$template->setOrder( $order );
		$email = $template->createEmail( $order->getEshop() );
		
		return $email->send();
	}
	
	
	public function sendNotifications(): bool
	{
		return $this->sendEMail( new EMailTemplate() );
	}
	
	
	public function getEMailTemplates(): array
	{
		$template = new EMailTemplate();
		
		return [$template];
	}
	
	public function getEventNameReadable(): string
	{
		return 'Order updated';
	}
	
	public function getEventCSSClass(): string
	{
		return 'event-updated';
	}
}
<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\Order\Cancel;


use JetApplication\EMail_TemplateProvider;
use JetApplication\MarketplaceIntegration;
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
		
		$payment_handler_module = $this->order->getPaymentMethod()?->getBackendModule();
		if($payment_handler_module) {
			$error_message = '';
			if(!$payment_handler_module->handleOrderCancellation( $this->order, $error_message )) {
				$this->event->setErrorMessage( $error_message );
				
				return false;
			}
		}
		
		
		return true;
	}

	public function handleInternals(): bool
	{
		WarehouseManagement::manageCancelledOrder( $this->order );
		
		return true;
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
		return 'Order cancelled';
	}
	
	public function getEventCSSClass(): string
	{
		return 'event-cancel';
	}
}
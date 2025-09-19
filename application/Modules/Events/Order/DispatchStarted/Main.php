<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\Order\DispatchStarted;


use JetApplication\EMail_TemplateProvider;
use JetApplication\MarketplaceIntegration;
use JetApplication\Order_Event_HandlerModule;
use JetApplication\OrderDispatch;
use JetApplication\OrderPersonalReceipt;


class Main extends Order_Event_HandlerModule implements EMail_TemplateProvider
{
	public function sendNotifications(): bool
	{
		return $this->sendEMail( new EMailTemplate() );
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
		$physical_products = $this->order->getHasPhysicalProducts();
		$virtual_products = $this->order->getHasVirtualProducts();
		
		if( $physical_products ) {
			$delivert_method = $this->order->getDeliveryMethod();
			
			if( $delivert_method->getKind()->isPersonalTakeoverInternal() ) {
				
				$dispatch = OrderPersonalReceipt::newByOrder( $this->order );
				$this->event->setContext( $dispatch );
				$this->event->save();

			} else {
				
				$dispatch = OrderDispatch::newByOrder( $this->order );
				$this->event->setContext( $dispatch );
				$this->event->save();
				
			}
		}
		
		if( $virtual_products ) {
			$this->handleVirtualProducts();
		}

		if( $virtual_products && !$physical_products ) {
			$this->order->dispatched();
		}
		
		return true;
	}
	
	protected function handleVirtualProducts() : void
	{
		if($this->order->getPaid()) {
			$virtual_products = $this->order->getVirtualProductOverview();
			
			foreach( $virtual_products as $vp ) {
				$vp->getProduct()->getKind()?->getVirtualProductHandler()?->dispatchOrder( $this->order, $vp );
			}
		}
	}
	
	public function getEventNameReadable(): string
	{
		return 'Order dispatch started';
	}
	
	public function getEventCSSClass(): string
	{
		return 'event-dispatch-started';
	}
	
	public function getEMailTemplates(): array
	{
		$template = new EMailTemplate();
		
		return [$template];
	}
	
}
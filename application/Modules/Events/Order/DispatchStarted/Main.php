<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\Order\DispatchStarted;


use JetApplication\Delivery_Kind;
use JetApplication\MarketplaceIntegration;
use JetApplication\OrderDispatch;
use JetApplication\Order_Event_HandlerModule;
use JetApplication\OrderPersonalReceipt;


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
		$physical_products = $this->order->getHasPhysicalProducts();
		$virtual_products = $this->order->getHasVirtualProducts();
		
		if( $physical_products ) {
			$delivert_method = $this->order->getDeliveryMethod();
			
			if( $delivert_method->getKindCode()==Delivery_Kind::PERSONAL_TAKEOVER_INTERNAL ) {
				
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
	
	public function getEventStyle(): string
	{
		return 'background-color: #00ddc1;color: #111111;';
	}
}
<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Events\Order\DispatchCanceled;

use JetApplication\MarketplaceIntegration;
use JetApplication\Order_Event_HandlerModule;
use JetApplication\OrderDispatch;

/**
 *
 */
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
		$order_dispatches = OrderDispatch::getListOfInProgress( $this->order->getProvidesContext() );
		foreach( $order_dispatches as $order_dispatch ) {
			$order_dispatch->cancel();
		}
		
		return true;
	}
	
	public function getEventNameReadable(): string
	{
		return 'Order dispatch cancelled';
	}
	
	public function getEventStyle(): string
	{
		return 'background-color: #ffaaaaaa;color: #111111;';
	}
}
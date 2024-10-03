<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Events\Order\Delivered;

use JetApplication\MarketplaceIntegration;
use JetApplication\Order_Event_HandlerModule;

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
		$virtual_products = $this->order->getVirtualProductOverview();

		foreach( $virtual_products as $vp ) {
			$vp->getProduct()->getKind()?->getVirtualProductHandler()?->dispatchOrder( $this->order, $vp );
		}
		
		return true;
	}
	
	public function getEventNameReadable(): string
	{
		return 'Order delivered';
	}
	
	public function getEventStyle(): string
	{
		return 'background-color: #00ffc1;color:#000000;';
	}
}
<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Events\Order\NotReadyForDispatch;

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
		return true;
	}
	
	public function getEventNameReadable(): string
	{
		return 'Order is not ready for dispatch';
	}
	
	public function getEventStyle(): string
	{
		return 'background-color: #f1002f;color: #ffffff;';
	}
}
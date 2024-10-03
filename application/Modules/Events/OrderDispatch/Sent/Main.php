<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Events\OrderDispatch\Sent;

use JetApplication\Order;
use JetApplication\OrderDispatch_Event_HandlerModule;
use JetApplication\WarehouseManagement;

/**
 *
 */
class Main extends OrderDispatch_Event_HandlerModule
{
	public function handleExternals(): bool
	{
		return true;
	}
	
	public function handleInternals(): bool
	{
		WarehouseManagement::manageOrderDispatchSent( $this->order_dispatch );
		
		switch($this->order_dispatch->getContextType()) {
			case Order::getProvidesContextType():
				$order = Order::get( $this->order_dispatch->getContextId() );
				$order?->dispatched();
				break;
			
			default:
				$this->event->setErrorMessage( 'Unknown context '.$this->order_dispatch->getContextType() );
				break;
		}
		
		return true;
	}
	
	public function sendNotifications(): bool
	{
		return true;
	}
	
	public function getEventNameReadable(): string
	{
		return 'Sent';
	}
	
	public function getEventStyle(): string
	{
		return '';
	}
}
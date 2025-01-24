<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Events\OrderPersonalReceipt\Prepared;

use JetApplication\Order;
use JetApplication\OrderPersonalReceipt_Event_HandlerModule;

/**
 *
 */
class Main extends OrderPersonalReceipt_Event_HandlerModule
{
	public function handleExternals(): bool
	{
		return true;
	}
	
	public function handleInternals(): bool
	{
		switch($this->order_personal_receipt->getContextType()) {
			case Order::getProvidesContextType():
				$order = Order::get( $this->order_personal_receipt->getContextId() );
				$order?->personalReceiptPrepared();
				break;
			
			default:
				$this->event->setErrorMessage( 'Unknown context '.$this->order_personal_receipt->getContextType() );
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
		return 'Prepared';
	}
	
	public function getEventStyle(): string
	{
		return '';
	}
}
<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\OrderPersonalReceipt\PreparationStarted;


use JetApplication\Order;
use JetApplication\OrderPersonalReceipt_Event_HandlerModule;


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
				$order?->personalReceiptPreparationStarted();
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
		return 'Preparation Started';
	}
	
	public function getEventCSSClass(): string
	{
		return 'event-dispatch-started';
	}
}
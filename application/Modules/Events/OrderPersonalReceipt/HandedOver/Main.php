<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Events\OrderPersonalReceipt\HandedOver;


use JetApplication\Order;
use JetApplication\OrderPersonalReceipt_Event_HandlerModule;
use JetApplication\WarehouseManagement;


class Main extends OrderPersonalReceipt_Event_HandlerModule
{
	public function handleExternals(): bool
	{
		return true;
	}
	
	public function handleInternals(): bool
	{
		WarehouseManagement::manageOrderPersonalReceiptHandedOver( $this->order_personal_receipt );
		
		switch($this->order_personal_receipt->getContextType()) {
			case Order::getProvidesContextType():
				$order = Order::get( $this->order_personal_receipt->getContextId() );
				$order?->handedOver();
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
		return 'Handed Over';
	}
	
	public function getEventStyle(): string
	{
		return '';
	}
}
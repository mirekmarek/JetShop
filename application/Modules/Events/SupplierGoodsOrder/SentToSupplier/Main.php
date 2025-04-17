<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\SupplierGoodsOrder\SentToSupplier;

use JetApplication\Supplier_GoodsOrder_Event;
use JetApplication\Supplier_GoodsOrder_Event_HandlerModule;
use JetApplication\Supplier_GoodsOrder_Status_ProblemDuringSending;

class Main extends Supplier_GoodsOrder_Event_HandlerModule
{
	
	public function handleInternals(): bool
	{
		$this->order->cleanupItems();
		
		
		$error_message = '';
		
		if( !$this->order->sendToTheSupplier( $error_message ) ) {
			$this->event->setErrorMessage( $error_message );
			
			$this->order->setStatus(
				Supplier_GoodsOrder_Status_ProblemDuringSending::get(),
				event_setup: function( Supplier_GoodsOrder_Event $event ) use ($error_message) {
					$event->setInternalNote( $error_message );
				}
			);
			
			return false;
		}
		
		
		return true;
	}
	
	public function getEventNameReadable(): string
	{
		return 'Order sent to the supplier';
	}
	
	public function getEventCSSClass(): string
	{
		return 'event-dispatched';
	}
}
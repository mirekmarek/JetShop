<?php
namespace JetShop;

use Jet\Logger;
use JetApplication\Order;
use JetApplication\Order_ChangeHistory;
use JetApplication\Order_Event;
use JetApplication\Order_Note;

trait Core_Order_Trait_Events {
	public const EVENT_NEW_ORDER = 'NewOrder';
	public const EVENT_CANCEL = 'Cancel';
	public const EVENT_PAID = 'Paid';
	public const EVENT_UPDATED = 'Updated';
	
	public const EVENT_READY_FOR_DISPATCH = 'ReadyForDispatch';
	public const EVENT_NOT_READY_FOR_DISPATCH = 'NotReadyForDispatch';
	public const EVENT_DISPATCH_STARTED = 'DispatchStarted';
	public const EVENT_DISPATCH_CANCELED = 'DispatchCanceled';
	public const EVENT_DISPATCHED = 'Dispatched';
	public const EVENT_DELIVERED = 'Delivered';
	
	public const EVENT_RETURNED = 'Returned';
	
	public const EVENT_PERSONAL_RECEIPT_PREPARATION_STARTED = 'PersonalReceiptPreparationStarted';
	public const EVENT_PERSONAL_RECEIPT_PREPARED = 'PersonalReceiptPrepared';
	public const EVENT_PERSONAL_RECEIPT_HANDED_OVER = 'PersonalReceiptHandedOver';
	
	public const EVENT_MESSAGE_FOR_CUSTOMER = 'MessageForCustomer';
	public const EVENT_INTERNAL_NOTE = 'InternalNote';
	
	
	public function createEvent( string $event ) : Order_Event
	{
		/**
		 * @var Order $this
		 */
		$e = Order_Event::newEvent( $this, $event );
		
		return $e;
	}
	
	
	protected function saveDispatchStateFlags() : void
	{
		static::updateData(
			data: [
				'ready_for_dispatch' => $this->ready_for_dispatch,
				'dispatch_started' => $this->dispatch_started,
				'dispatched' => $this->dispatched,
				'delivered' => $this->delivered,
			],
			where: [
				'id' => $this->id
			]
		);
		
	}
	
	public function newOrder() : void
	{
		$this->createEvent( Order::EVENT_NEW_ORDER )->handleImmediately();
	}
	
	public function readyForDispatch() : void
	{
		$this->ready_for_dispatch = true;
		$this->dispatch_started = false;
		$this->dispatched = false;
		$this->delivered = false;
		
		$this->saveDispatchStateFlags();
		
		Logger::info(
			event: 'order:ready_for_dispatch',
			event_message: 'Order '.$this->getNumber().' is ready for dispatch',
			context_object_id:
			$this->id,context_object_name: $this->getNumber(),
			context_object_data: $this
		);
		
		$this->createEvent( Order::EVENT_READY_FOR_DISPATCH )->handleImmediately();
	}
	
	public function notReadyForDispatch() : void
	{
		$this->ready_for_dispatch = false;
		$this->dispatch_started = false;
		$this->dispatched = false;
		$this->delivered = false;
		
		$this->saveDispatchStateFlags();
		
		Logger::info(
			event: 'order:not_ready_for_dispatch',
			event_message: 'Order '.$this->getNumber().' is not ready for dispatch',
			context_object_id:
			$this->id,context_object_name: $this->getNumber(),
			context_object_data: $this
		);
		
		$this->createEvent( Order::EVENT_NOT_READY_FOR_DISPATCH )->handleImmediately();
	}
	
	
	
	public function dispatchStarted() : void
	{
		$this->ready_for_dispatch = true;
		$this->dispatch_started = true;
		$this->dispatched = false;
		$this->delivered = false;
		
		$this->saveDispatchStateFlags();
		
		Logger::info(
			event: 'order:dispatch_started',
			event_message: 'Order '.$this->getNumber().' dispatch started',
			context_object_id:
			$this->id,context_object_name: $this->getNumber(),
			context_object_data: $this
		);
		
		$this->createEvent( Order::EVENT_DISPATCH_STARTED )->handleImmediately();
	}
	
	public function cancelDispatch() : void
	{
		$this->ready_for_dispatch = true;
		$this->dispatch_started = false;
		$this->dispatched = false;
		$this->delivered = false;
		
		$this->saveDispatchStateFlags();
		
		Logger::info(
			event: 'order:dispatch_canceled',
			event_message: 'Order '.$this->getNumber().' dispatch canceled',
			context_object_id:
			$this->id,context_object_name: $this->getNumber(),
			context_object_data: $this
		);
		
		$this->createEvent( Order::EVENT_DISPATCH_CANCELED )->handleImmediately();
	}
	
	public function dispatched() : void
	{
		$this->ready_for_dispatch = true;
		$this->dispatch_started = true;
		$this->dispatched = true;
		$this->delivered = false;
		
		$this->saveDispatchStateFlags();
		
		Logger::info(
			event: 'order:dispatched',
			event_message: 'Order '.$this->getNumber().' was dispatched',
			context_object_id:
			$this->id,context_object_name: $this->getNumber(),
			context_object_data: $this
		);
		
		$this->createEvent( Order::EVENT_DISPATCHED )->handleImmediately();
	}
	
	public function delivered() : void
	{
		$this->ready_for_dispatch = true;
		$this->dispatch_started = true;
		$this->dispatched = true;
		$this->delivered = true;
		
		$this->saveDispatchStateFlags();
		
		Logger::info(
			event: 'order:delivered',
			event_message: 'Order '.$this->getNumber().' was delivered',
			context_object_id:
			$this->id,context_object_name: $this->getNumber(),
			context_object_data: $this
		);
		
		$this->createEvent( Order::EVENT_DELIVERED )->handleImmediately();
	}
	
	public function paid() : void
	{
		if($this->paid) {
			return;
		}
		
		Logger::info(
			event: 'order:paid',
			event_message: 'Order '.$this->getNumber().' was paid',
			context_object_id:
			$this->id,context_object_name: $this->getNumber(),
			context_object_data: $this
		);
		
		$this->paid = true;
		$this->save();
		
		$this->createEvent(Order::EVENT_PAID)->handleImmediately();
		
		$this->checkIsReady();
	}
	
	
	public function updated( Order_ChangeHistory $change ) : void
	{
		$event = $this->createEvent( Order::EVENT_UPDATED );
		$event->setContext( $change );
		
		$event->handleImmediately();
	}
	
	
	public function cancel( string $comments ) : void
	{
		if($this->cancelled) {
			return;
		}
		
		$this->cancelled = true;
		$this->save();
		
		$event = $this->createEvent( Order::EVENT_CANCEL );
		$event->setNoteForCustomer( $comments );
		$event->handleImmediately();
	}
	
	public function newNote( Order_Note $note ) : void
	{
		if( $note->getSentToCustomer() ) {
			$this->messageForCustomer( $note );
		} else {
			$this->internalNote( $note );
		}
	}
	
	public function messageForCustomer( Order_Note $note ) : void
	{
		$event = $this->createEvent( Order::EVENT_MESSAGE_FOR_CUSTOMER );
		$event->setContext( $note );
		$event->handleImmediately();
	}
	
	public function internalNote( Order_Note $note ) : void
	{
		$event = $this->createEvent( Order::EVENT_INTERNAL_NOTE );
		$event->setContext( $note );
		$event->handleImmediately();
	}
	
	
	public function handedOver() : void
	{
		$this->ready_for_dispatch = true;
		$this->dispatch_started = true;
		$this->dispatched = true;
		$this->delivered = true;
		
		$this->saveDispatchStateFlags();
		
		Logger::info(
			event: 'order:handed_over',
			event_message: 'Order '.$this->getNumber().' was handed over',
			context_object_id:
			$this->id,context_object_name: $this->getNumber(),
			context_object_data: $this
		);
		
		TODO: $this->createEvent( Order::EVENT_PERSONAL_RECEIPT_HANDED_OVER )->handleImmediately();
	}
	
	
	public function personalReceiptPreparationStarted() : void
	{
		$this->ready_for_dispatch = true;
		$this->dispatch_started = true;
		$this->dispatched = false;
		$this->delivered = false;
		
		$this->saveDispatchStateFlags();
		
		Logger::info(
			event: 'order:personal_peceipt_preparation_started',
			event_message: 'Order '.$this->getNumber().' personal peceipt preparation started',
			context_object_id:
			$this->id,context_object_name: $this->getNumber(),
			context_object_data: $this
		);
		
		
		
		$this->createEvent( Order::EVENT_PERSONAL_RECEIPT_PREPARATION_STARTED )->handleImmediately();
	}
	
	
	public function personalReceiptPrepared() : void
	{
		$this->ready_for_dispatch = true;
		$this->dispatch_started = true;
		$this->dispatched = true;
		$this->delivered = false;
		
		$this->saveDispatchStateFlags();
		
		Logger::info(
			event: 'order:personal_peceipt_prepared',
			event_message: 'Order '.$this->getNumber().' personal peceipt prepared',
			context_object_id:
			$this->id,context_object_name: $this->getNumber(),
			context_object_data: $this
		);
		
		$this->createEvent( Order::EVENT_PERSONAL_RECEIPT_PREPARED )->handleImmediately();
	}
}
<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\EShopEntity_Event;
use JetApplication\Order_ChangeHistory;
use JetApplication\Order_Event;
use JetApplication\Order_Event_DispatchCanceled;
use JetApplication\Order_Event_InternalNote;
use JetApplication\Order_Event_MessageForCustomer;
use JetApplication\Order_Event_NewOrder;
use JetApplication\Order_Event_NotReadyForDispatch;
use JetApplication\Order_Event_Paid;
use JetApplication\Order_Event_Updated;
use JetApplication\Order_Note;
use JetApplication\Order_Status_Cancelled;
use JetApplication\Order_Status_Delivered;
use JetApplication\Order_Status_Dispatched;
use JetApplication\Order_Status_DispatchStarted;
use JetApplication\Order_Status_PersonalReceiptPreparationStarted;
use JetApplication\Order_Status_PersonalReceiptPrepared;
use JetApplication\Order_Status_ReadyForDispatch;
use JetApplication\Order_Status_HandedOver;

trait Core_Order_Trait_Events {
	
	public function createEvent( EShopEntity_Event|Order_Event $event ) : Order_Event
	{
		$event->init( $this->getEshop() );
		$event->setOrder( $this );
		
		return $event;
	}
	
	
	public function newOrder() : void
	{
		$this->createEvent( Order_Event_NewOrder::new() )->handleImmediately();
	}
	
	
	public function dispatchStarted() : void
	{
		$this->setStatus( Order_Status_DispatchStarted::get() );
	}
	
	public function dispatched() : void
	{
		$this->setStatus( Order_Status_Dispatched::get() );
	}
	
	public function delivered() : void
	{
		$this->setStatus( Order_Status_Delivered::get() );
	}
	
	
	public function handedOver() : void
	{
		$this->setStatus( Order_Status_HandedOver::get() );
	}
	
	
	public function personalReceiptPreparationStarted() : void
	{
		$this->setStatus( Order_Status_PersonalReceiptPreparationStarted::get() );
	}
	
	
	public function personalReceiptPrepared() : void
	{
		$this->setStatus( Order_Status_PersonalReceiptPrepared::get() );
	}
	
	
	public function cancelDispatch() : void
	{
		//TODO: virtual status
		$this->setFlags(
			[
				'ready_for_dispatch' => true,
				'dispatch_started' => false,
				'dispatched' => false,
				'delivered' => false,
			]
		);
		$this->setStatusByFlagState();
		
		$this->createEvent( Order_Event_DispatchCanceled::new() )->handleImmediately();
	}
	
	public function paid() : void
	{
		//TODO: virtual status
		if($this->paid) {
			return;
		}
		
		$this->setFlags([
			'paid' => true,
		]);
		$this->setStatusByFlagState();
		
		$this->createEvent(Order_Event_Paid::new())->handleImmediately();
	}
	
	public function checkIsReady() : void
	{
		//TODO: virtual status
		if($this->getDeliveryMethod()->isEDelivery()) {
			return;
		}
		
		if(
			(
				$this->paid ||
				!$this->payment_required
			) &&
			$this->all_items_available
		) {
			if(!$this->ready_for_dispatch) {
				$this->setStatus( Order_Status_ReadyForDispatch::get() );
			}
		} else {
			if($this->ready_for_dispatch) {
				
				$this->setFlags(
					[
						'ready_for_dispatch' => false,
						'dispatch_started' => false,
						'dispatched' => false,
						'delivered' => false,
					]
				);
				$this->setStatusByFlagState();
				
				$this->createEvent( Order_Event_NotReadyForDispatch::new() )->handleImmediately();
			}
		}
	}
	
	
	public function cancel( string $comments ) : void
	{
		$this->setStatus( Order_Status_Cancelled::get(), true );
	}
	
	
	public function updated( Order_ChangeHistory $change ) : void
	{
		$event = $this->createEvent( Order_Event_Updated::new() );
		$event->setContext( $change );
		
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
		$event = $this->createEvent( Order_Event_MessageForCustomer::new() );
		$event->setContext( $note );
		$event->handleImmediately();
	}
	
	public function internalNote( Order_Note $note ) : void
	{
		$event = $this->createEvent( Order_Event_InternalNote::new() );
		$event->setContext( $note );
		$event->handleImmediately();
	}
	
}
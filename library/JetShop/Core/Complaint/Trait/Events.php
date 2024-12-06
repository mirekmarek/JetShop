<?php
namespace JetShop;

use JetApplication\Complaint;
use JetApplication\Complaint_ChangeHistory;
use JetApplication\Complaint_Event;
use JetApplication\Complaint_Note;

trait Core_Complaint_Trait_Events {
	
	public const EVENT_NEW_UNFINISHED_COMPLAINT = 'NewUnfinishedComplaint';
	public const EVENT_NEW_COMPLAINT_FINISHED = 'NewComplaintFinished';
	public const EVENT_UPDATED = 'Updated';
	
	public const EVENT_PROCESSING_STARTED = 'ProcessingStarted';
	public const EVENT_CLARIFICATION_REQUIRED = 'ClarificationRequired';
	
	public const EVENT_REJECTED = 'Rejected';
	
	public const EVENT_CANCELLED = 'Cancelled';
	
	public const EVENT_ACCEPTED_MONEY_REFUND = 'AcceptedMoneyRefund';
	public const EVENT_ACCEPTED_SENT_FOR_REPAIR = 'AcceptedSentForRepair';
	public const EVENT_ACCEPTED_REPAIRED = 'AcceptedRepaired';
	public const EVENT_ACCEPTED_NEW_GOODS_WILL_BE_SEND = 'AcceptedNewGoodsWillBeSend';
	
	public const EVENT_NEW_PRODUCT_DISPATCHED = 'NewProductDispatched';
	public const EVENT_REPAIRED_PRODUCT_DISPATCHED = 'RepairedProductDispatched';

	public const EVENT_DELIVERED = 'Delivered';
	public const EVENT_RETURNED = 'Returned';
	
	public const EVENT_MESSAGE_FOR_CUSTOMER = 'MessageForCustomer';
	public const EVENT_INTERNAL_NOTE = 'InternalNote';
	
	
	public function createEvent( string $event ) : Complaint_Event
	{
		/**
		 * @var Complaint $this
		 */
		$e = Complaint_Event::newEvent( $this, $event );
		
		return $e;
	}
	
	public function newUnfinishedComplaint() : void
	{
		$this->createEvent( static::EVENT_NEW_UNFINISHED_COMPLAINT )->handleImmediately();
	}
	
	public function newComplaintFinished() : void
	{
		$this->completed = true;
		$this->save();
		
		$this->createEvent( static::EVENT_NEW_COMPLAINT_FINISHED )->handleImmediately();
	}
	
	public function rejected( string $message_for_customer ) : void
	{
		$this->rejected = true;
		$this->save();
		
		$event = $this->createEvent( static::EVENT_REJECTED );
		
		$event->setNoteForCustomer( $message_for_customer );
		$event->handleImmediately();

		
	}
	
	public function newProductDispatched( int $product_id, int $delivery_method, string $delivery_point_code, string $note_for_customer ) : void
	{
		//TODO:
		$event = $this->createEvent( static::EVENT_NEW_PRODUCT_DISPATCHED );
		//$event->setContext1( $product_id );
		//$event->setContext2( $delivery_method );
		//$event->setContext3( $delivery_point_code );
		
		$event->setNoteForCustomer( $note_for_customer );
		$event->handleImmediately();
		
	}
	
	public function newNote( Complaint_Note $note ) : void
	{
		if( $note->getSentToCustomer() ) {
			$this->messageForCustomer( $note );
		} else {
			$this->internalNote( $note );
		}
	}
	
	public function messageForCustomer( Complaint_Note $note ) : void
	{
		$event = $this->createEvent( Complaint::EVENT_MESSAGE_FOR_CUSTOMER );
		$event->setContext( $note );
		$event->handleImmediately();
	}
	
	public function internalNote( Complaint_Note $note ) : void
	{
		$event = $this->createEvent( Complaint::EVENT_INTERNAL_NOTE );
		$event->setContext( $note );
		$event->handleImmediately();
	}
	
	
	public function updated( Complaint_ChangeHistory $change ) : void
	{
		$event = $this->createEvent( Complaint::EVENT_UPDATED );
		$event->setContext( $change );
		
		$event->handleImmediately();
	}
	
}
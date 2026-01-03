<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\Complaint_ChangeHistory;
use JetApplication\Complaint_Event;
use JetApplication\Complaint_Event_InternalNote;
use JetApplication\Complaint_Event_MessageForCustomer;
use JetApplication\Complaint_Event_NewProductDispatched;
use JetApplication\Complaint_Event_NewUnfinishedComplaint;
use JetApplication\Complaint_Event_Rejected;
use JetApplication\Complaint_Event_Updated;
use JetApplication\Complaint_Note;
use JetApplication\Complaint_Status_New;
use JetApplication\EShopEntity_Event;

trait Core_Complaint_Trait_Events {
	
	
	public function createEvent( EShopEntity_Event|Complaint_Event $event ) : Complaint_Event
	{
		$event->init( $this->getEshop() );
		$event->setComplaint( $this );
		
		return $event;
	}
	
	public function newUnfinishedComplaint() : void
	{
		$this->createEvent( Complaint_Event_NewUnfinishedComplaint::new() )->handleImmediately();
	}
	
	public function newComplaintFinished() : void
	{
		$this->completed = true;
		$this->save();
		
		$this->setStatus( Complaint_Status_New::get() );
	}
	
	public function rejected( string $message_for_customer ) : void
	{
		$this->rejected = true;
		$this->save();
		
		$event = $this->createEvent( Complaint_Event_Rejected::new() );
		
		$event->setNoteForCustomer( $message_for_customer );
		
		$event->handleImmediately();
	}
	
	public function newProductDispatched( int $product_id, int $delivery_method, string $delivery_point_code, string $note_for_customer ) : void
	{
		//TODO:
		$event = $this->createEvent( Complaint_Event_NewProductDispatched::new() );
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
		$event = $this->createEvent( Complaint_Event_MessageForCustomer::new() );
		$event->setContext( $note );
		$event->handleImmediately();
	}
	
	public function internalNote( Complaint_Note $note ) : void
	{
		$event = $this->createEvent( Complaint_Event_InternalNote::new() );
		$event->setContext( $note );
		$event->handleImmediately();
	}
	
	
	public function updated( Complaint_ChangeHistory $change ) : void
	{
		$event = $this->createEvent( Complaint_Event_Updated::new() );
		$event->setContext( $change );
		
		$event->handleImmediately();
	}
	
}
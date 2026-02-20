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
use JetApplication\Order_Event_InternalNote;
use JetApplication\Order_Event_MessageForCustomer;
use JetApplication\Order_Event_NewOrder;
use JetApplication\Order_Event_Updated;
use JetApplication\Order_Note;
use JetApplication\Order_Status_Cancelled;
use JetApplication\Order_Status_Delivered;
use JetApplication\Order_Status_Dispatched;
use JetApplication\Order_Status_DispatchStarted;
use JetApplication\Order_Status_PersonalReceiptPreparationStarted;
use JetApplication\Order_Status_PersonalReceiptPrepared;
use JetApplication\Order_Status_HandedOver;
use JetApplication\Order_VirtualStatus_CancelDispatch;
use JetApplication\Order_VirtualStatus_CheckIsReady;
use JetApplication\Order_VirtualStatus_Paid;

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
		$this->setStatus( Order_VirtualStatus_CancelDispatch::get() );
	}
	
	public function paid() : void
	{
		$this->setStatus( Order_VirtualStatus_Paid::get() );
	}
	
	public function checkIsReady() : void
	{
		$this->setStatus( Order_VirtualStatus_CheckIsReady::get() );
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
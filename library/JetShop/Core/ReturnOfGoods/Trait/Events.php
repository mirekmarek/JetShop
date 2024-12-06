<?php
namespace JetShop;

use JetApplication\ReturnOfGoods;
use JetApplication\ReturnOfGoods_ChangeHistory;
use JetApplication\ReturnOfGoods_Event;
use JetApplication\ReturnOfGoods_Note;

trait Core_ReturnOfGoods_Trait_Events
{
	public const EVENT_DONE_ACCEPTED = 'DoneAccepted';
	public const EVENT_DONE_REJECTED = 'DoneRejected';
	public const EVENT_NEW_UNFINISHED_RETURN_OF_GOODS = 'NewUnfinishedReturnOfGoods';
	public const EVENT_RETURN_OF_GOODS_FINISHED = 'ReturnOfGoodsFinished';
	public const EVENT_UPDATED = 'Updated';
	
	public const EVENT_MESSAGE_FOR_CUSTOMER = 'MessageForCustomer';
	public const EVENT_INTERNAL_NOTE = 'InternalNote';
	
	
	
	public function createEvent( string $event ) : ReturnOfGoods_Event
	{
		/**
		 * @var ReturnOfGoods $this
		 */
		$e = ReturnOfGoods_Event::newEvent( $this, $event );
		
		return $e;
	}
	
	public function newUnfinishedReturnOfGoods() : void
	{
		$this->createEvent( static::EVENT_NEW_UNFINISHED_RETURN_OF_GOODS )->handleImmediately();
	}
	
	
	public function newReturnOfGoodsFinished() : void
	{
		$this->completed = true;
		$this->save();
		
		$this->createEvent( static::EVENT_RETURN_OF_GOODS_FINISHED )->handleImmediately();
	}
	
	
	public function newNote( ReturnOfGoods_Note $note ) : void
	{
		if( $note->getSentToCustomer() ) {
			$this->messageForCustomer( $note );
		} else {
			$this->internalNote( $note );
		}
	}
	
	public function messageForCustomer( ReturnOfGoods_Note $note ) : void
	{
		$event = $this->createEvent( ReturnOfGoods::EVENT_MESSAGE_FOR_CUSTOMER );
		$event->setContext( $note );
		$event->handleImmediately();
	}
	
	public function internalNote( ReturnOfGoods_Note $note ) : void
	{
		$event = $this->createEvent( ReturnOfGoods::EVENT_INTERNAL_NOTE );
		$event->setContext( $note );
		$event->handleImmediately();
	}
	
	public function updated( ReturnOfGoods_ChangeHistory $change ) : void
	{
		$event = $this->createEvent( ReturnOfGoods::EVENT_UPDATED );
		$event->setContext( $change );
		
		$event->handleImmediately();
	}
	
}
<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\EShopEntity_Event;
use JetApplication\ReturnOfGoods_ChangeHistory;
use JetApplication\ReturnOfGoods_Event;
use JetApplication\ReturnOfGoods_Event_InternalNote;
use JetApplication\ReturnOfGoods_Event_MessageForCustomer;
use JetApplication\ReturnOfGoods_Event_NewUnfinishedReturnOfGoods;
use JetApplication\ReturnOfGoods_Event_Updated;
use JetApplication\ReturnOfGoods_Note;

trait Core_ReturnOfGoods_Trait_Events
{
	
	public function createEvent( EShopEntity_Event|ReturnOfGoods_Event $event ) : ReturnOfGoods_Event
	{
		$event->init( $this->getEshop() );
		$event->setReturnOfGoods( $this );
		
		return $event;
	}
	
	public function newUnfinishedReturnOfGoods() : void
	{
		$this->createEvent( ReturnOfGoods_Event_NewUnfinishedReturnOfGoods::new() )->handleImmediately();
	}
	
	
	public function newReturnOfGoodsFinished() : void
	{
		$this->completed = true;
		$this->save();
		
		$this->createEvent( Core_ReturnOfGoods_Event_ReturnOfGoodsFinished::new() )->handleImmediately();
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
		$event = $this->createEvent( ReturnOfGoods_Event_MessageForCustomer::new() );
		$event->setContext( $note );
		$event->handleImmediately();
	}
	
	public function internalNote( ReturnOfGoods_Note $note ) : void
	{
		$event = $this->createEvent( ReturnOfGoods_Event_InternalNote::new() );
		$event->setContext( $note );
		$event->handleImmediately();
	}
	
	public function updated( ReturnOfGoods_ChangeHistory $change ) : void
	{
		$event = $this->createEvent( ReturnOfGoods_Event_Updated::new() );
		$event->setContext( $change );
		
		$event->handleImmediately();
	}
	
}
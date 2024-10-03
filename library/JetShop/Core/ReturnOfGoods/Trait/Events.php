<?php
namespace JetShop;

use JetApplication\ReturnOfGoods;
use JetApplication\ReturnOfGoods_Event;

trait Core_ReturnOfGoods_Trait_Events
{
	public const EVENT_DONE_ACCEPTED = 'DoneAccepted';
	public const EVENT_DONE_REJECTED = 'DoneRejected';
	public const EVENT_NEW_UNFINISHED_RETURN_OF_GOODS = 'NewUnfinishedReturnOfGoods';
	public const EVENT_RETURN_OF_GOODS_FINISHED = 'ReturnOfGoodsFinished';
	
	
	
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
	

}
<?php
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\Order;
use JetApplication\Order_Status;

trait Core_Order_Trait_Status {
	
	protected static array $flags =  [
				'cancelled',
				'payment_required',
				'paid',
				'all_items_available',
				'ready_for_dispatch',
				'dispatch_started',
				'dispatched',
				'delivered',
				'returned',
	];
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $cancelled = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $payment_required = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $paid = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $all_items_available = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $ready_for_dispatch = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $dispatch_started = false;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $dispatched = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $delivered = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $returned = false;
	
	
	public function isCancelled() : bool
	{
		return $this->cancelled;
	}
	
	public function getPaymentRequired(): bool
	{
		return $this->payment_required;
	}
	
	public function setPaymentRequired( bool $payment_required ): void
	{
		$this->payment_required = $payment_required;
	}
	
	public function getPaid(): bool
	{
		return $this->paid;
	}
	
	public function setPaid( bool $paid ): void
	{
		$this->paid = $paid;
	}
	
	public function getAllItemsAvailable() : bool
	{
		return $this->all_items_available;
	}
	
	public function setAllItemsAvailable( bool $all_items_available ): void
	{
		$this->all_items_available = $all_items_available;
	}
	
	public function getReadyForDispatch(): bool
	{
		return $this->ready_for_dispatch;
	}
	
	public function setReadyForDispatch( bool $ready_for_dispatch ): void
	{
		$this->ready_for_dispatch = $ready_for_dispatch;
	}
	
	public function getDispatchStarted(): bool
	{
		return $this->dispatch_started;
	}
	
	public function setDispatchStarted( bool $dispatch_started ): void
	{
		$this->dispatch_started = $dispatch_started;
	}
	
	public function getDispatched(): bool
	{
		return $this->dispatched;
	}
	
	public function setDispatched( bool $dispatched ): void
	{
		$this->dispatched = $dispatched;
	}
	
	
	public function getDelivered(): bool
	{
		return $this->delivered;
	}
	
	public function setDelivered( bool $delivered ): void
	{
		$this->delivered = $delivered;
	}
	
	
	
	public function checkIsReady() : void
	{
		if(
			(
				$this->paid ||
				!$this->payment_required
			) &&
			$this->all_items_available
		) {
			if(!$this->ready_for_dispatch) {
				$this->ready_for_dispatch  = true;
				$this->save();
				
				$this->readyForDispatch();
			}
		} else {
			if($this->ready_for_dispatch) {
				$this->ready_for_dispatch  = false;
				$this->save();
				
				$this->notReadyForDispatch();
			}
		}
	}
	
	
	
	public function isEditable(): bool
	{
		if(
			$this->cancelled ||
			$this->delivered ||
			$this->dispatch_started
		) {
			return false;
		}
		
		
		return true;
	}
	
	
	
	public function setEditable( bool $editable ): void
	{
	}
	
	public function getFlags() : array
	{
		$res = [];
		foreach( static::$flags as $flag ) {
			$res[$flag] = $this->{$flag};
		}
		
		return $res;
	}
	
	public function setFlags( array $flags ) : void
	{
		foreach( static::$flags as $flag ) {
			if(
				!array_key_exists($flag, $flags) ||
				$flags[$flag] === null
			) {
				continue;
			}
			
			$this->{$flag} = (bool)$flags[$flag];
		}
		
	}
	
	public function getStatus() : ?Order_Status
	{
		/**
		 * @var Order $this
		 */
		foreach(Order_Status::getList() as $status) {
			if($status::resolve( $this )) {
				return $status;
			}
		}
		
		return null;
	}
	
}
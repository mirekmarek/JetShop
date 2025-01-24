<?php
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\Complaint;
use JetApplication\Complaint_Status;
use JetApplication\Complaint_DispatchStatus;

trait Core_Complaint_Trait_Status {
	
	protected static array $flags =  [
		'cancelled',
		
		'completed',
		'clarification_required',
		'being_processed',
		
		'rejected',
		
		'accepted',
		
		'money_refund',
		'sent_for_repair',
		'repaired',
		'send_new_products',
	];
	
	protected static array $dispatch_flags =  [
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
	protected bool $completed = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $cancelled = false;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $clarification_required = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $being_processed = false;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $rejected = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $accepted = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $money_refund = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $sent_for_repair = false;

	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $repaired = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $send_new_products = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $new_product_available = false;
	
	
	
	
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
	
	public function isCompleted() : bool
	{
		return $this->completed;
	}
	
	
	
	public function checkIsReady() : void
	{
		
		if(
			$this->repaired ||
			$this->new_product_available
		) {
			$this->ready_for_dispatch  = true;
		} else {
			$this->ready_for_dispatch  = false;
		}
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
	
	
	public function getStatus() : ?Complaint_Status
	{
		/**
		 * @var Complaint $this
		 */
		foreach(Complaint_Status::getList() as $status) {
			if($status::resolve( $this )) {
				return $status;
			}
		}
		
		return null;
	}
	
	
	
	
	
	
	public function getDispatchFlags() : array
	{
		$res = [];
		foreach( static::$dispatch_flags as $flag ) {
			$res[$flag] = $this->{$flag};
		}
		
		return $res;
	}
	
	public function setDispatchFlags( array $flags ) : void
	{
		foreach( static::$dispatch_flags as $flag ) {
			if(
				!array_key_exists($flag, $flags) ||
				$flags[$flag] === null
			) {
				continue;
			}
			
			$this->{$flag} = (bool)$flags[$flag];
		}
		
	}
	
	
	public function getDispatchStatus() : ?Complaint_DispatchStatus
	{
		if(
			$this->cancelled ||
			(
				!$this->repaired &&
				!$this->send_new_products
			)
		) {
			return null;
		}
		
		/**
		 * @var Complaint $this
		 */
		foreach(Complaint_DispatchStatus::getList() as $status) {
			if($status::resolve( $this )) {
				return $status;
			}
		}
		
		return null;
	}

}
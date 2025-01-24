<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\ReturnOfGoods;
use JetApplication\ReturnOfGoods_Status;

trait Core_ReturnOfGoods_Trait_Status
{
	protected static array $flags =  [
		'cancelled',
		
		'completed',
		'clarification_required',
		'being_processed',
		
		'rejected',
		
		'accepted',
		
		'money_refund',
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
	
	
	public function isCancelled() : bool
	{
		return $this->cancelled;
	}
	
	public function isCompleted() : bool
	{
		return $this->completed;
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
	
	
	public function getStatus() : ?ReturnOfGoods_Status
	{
		/**
		 * @var ReturnOfGoods $this
		 */
		foreach(ReturnOfGoods_Status::getList() as $status) {
			if($status::resolve( $this )) {
				return $status;
			}
		}
		
		return null;
	}
	
	
	

}
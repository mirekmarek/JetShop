<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;



use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\Complaint_Status;

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


	public static function getStatusList() : array
	{
		return Complaint_Status::getList();
	}
}
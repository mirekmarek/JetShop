<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
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

	public static function getStatusList(): array
	{
		return ReturnOfGoods_Status::getList();
	}
}
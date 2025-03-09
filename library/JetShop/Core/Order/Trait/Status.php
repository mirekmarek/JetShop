<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;



use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\EShopEntity_Status;
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
	
	
	/**
	 * @return EShopEntity_Status[]
	 */
	public static function getStatusList(): array
	{
		return Order_Status::getList();
	}
	
	
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
	
	public function setEditable( bool $editable ): void
	{
	}

}
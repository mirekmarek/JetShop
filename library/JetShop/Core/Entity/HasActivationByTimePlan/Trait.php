<?php
namespace JetShop;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_DateTime;
use JetApplication\Entity_HasActivation_Trait;

trait Core_Entity_HasActivationByTimePlan_Trait {
	
	use Entity_HasActivation_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_DATE_TIME,
		label: 'Active from:',
		error_messages: [
			Form_Field_DateTime::ERROR_CODE_EMPTY          => 'Please enter date and time',
			Form_Field_DateTime::ERROR_CODE_INVALID_FORMAT => 'Please enter date and time'
		]
	)]
	protected ?Data_DateTime $active_from = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_DATE_TIME,
		label: 'Active till:',
		error_messages: [
			Form_Field_DateTime::ERROR_CODE_EMPTY          => 'Please enter date and time',
			Form_Field_DateTime::ERROR_CODE_INVALID_FORMAT => 'Please enter date and time'
		]
	)]
	protected ?Data_DateTime $active_till = null;
	
	public function setActiveFrom( Data_DateTime|string|null $value ): void
	{
		$this->active_from = Data_DateTime::catchDateTime( $value );
	}
	
	public function getActiveFrom(): Data_DateTime|null
	{
		return $this->active_from;
	}
	
	public function setActiveTill( Data_DateTime|string|null $value ): void
	{
		$this->active_till = Data_DateTime::catchDateTime( $value );
	}
	
	public function getActiveTill(): Data_DateTime|null
	{
		return $this->active_till;
	}
	
	public function isActive() : bool
	{
		if(!$this->active_from && !$this->active_till) {
			return $this->is_active;
		}
		
		$is_active_by_plan = $this->isActiveByTimePlan();
		
		if(
			$this->is_active != $is_active_by_plan
		) {
			$this->is_active = $is_active_by_plan;
			static::updateData(['is_active'=>$this->is_active], ['id'=>$this->id]);
		}
		
		return $this->is_active;
	}
	
	public function hasTimePlan() : bool
	{
		if(!$this->active_from && !$this->active_till) {
			return false;
		}
		
		return true;
	}
	
	public function isActiveByTimePlan() : bool
	{
		if(!$this->hasTimePlan()) {
			return true;
		}
		
		$now = Data_DateTime::now();
		if(
			$this->active_till &&
			$this->active_till<$now
		) {
			return false;
		}
		
		if(
			$this->active_from &&
			$this->active_from>$now
		) {
			return false;
		}
		
		return true;
	}
	
	public function isExpiredByTimePlan() : bool
	{
		if(
			$this->isActiveByTimePlan() ||
			!$this->active_till
		) {
			return false;
		}
		
		$now = Data_DateTime::now();
		return $now<$this->active_till;
	}
	
	public function isWaitingByTimePlan() : bool
	{
		if(
			$this->isActiveByTimePlan() ||
			!$this->active_from
		) {
			return false;
		}
		
		$now = Data_DateTime::now();
		
		
		return $this->active_from>$now;
	}
	
	public static function handleTimePlan() : void
	{
		$data = static::dataFetchAll(
			select: [
				'id',
				'is_active',
				'active_from',
				'active_till'
			]
		);
		
		$now = Data_DateTime::now();
		foreach($data as $d) {
			if(
				!$d['active_from'] &&
				!$d['active_till']
			) {
				continue;
			}
			
			$is_active = true;
			if(
				$d['active_from'] &&
				$d['active_from']>$now
			) {
				$is_active = false;
			}
			
			if(
				$d['active_till'] &&
				$d['active_till']<$now
			) {
				$is_active = false;
			}
			
			if($is_active!=$d['is_active']) {
				static::updateData(['is_active'=>$is_active], ['id'=>$d['id']]);
			}
			
		}
	}
	
}
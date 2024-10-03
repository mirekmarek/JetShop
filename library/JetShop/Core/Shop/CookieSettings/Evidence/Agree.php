<?php
/**
 *
 */

namespace JetShop;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;


use JetApplication\Entity_WithShopRelation;

#[DataModel_Definition(
	name: 'cookie_settings_evidence_agree',
	database_table_name: 'cookie_settings_evidence_agree'
)]
class Core_Shop_CookieSettings_Evidence_Agree extends Entity_WithShopRelation
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $IP;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected Data_DateTime $date_time;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $groups;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	protected bool $complete_agree;
	

	public function getIP(): string
	{
		return $this->IP;
	}
	
	public function setIP( string $IP ): void
	{
		$this->IP = $IP;
	}
	
	public function getDateTime(): Data_DateTime
	{
		return $this->date_time;
	}
	
	public function setDateTime( Data_DateTime $date_time ): void
	{
		$this->date_time = $date_time;
	}
	
	public function getGroups(): array
	{
		if(!$this->groups) {
			return [];
		}
		return explode('|', $this->groups);
	}
	
	public function setGroups( array $groups ): void
	{
		$this->groups = implode('|', $groups);
	}
	
	public function getCompleteAgree(): bool
	{
		return $this->complete_agree;
	}

	public function setCompleteAgree( bool $complete_agree ): void
	{
		$this->complete_agree = $complete_agree;
	}

	
	
}

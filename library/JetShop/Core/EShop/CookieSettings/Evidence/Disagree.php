<?php
/**
 *
 */

namespace JetShop;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;


use JetApplication\EShopEntity_WithEShopRelation;

#[DataModel_Definition(
	name: 'cookie_settings_evidence_disagree',
	database_table_name: 'cookie_settings_evidence_disagree'
)]
class Core_EShop_CookieSettings_Evidence_Disagree extends EShopEntity_WithEShopRelation
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $IP = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $date_time = null;
	
	
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
}

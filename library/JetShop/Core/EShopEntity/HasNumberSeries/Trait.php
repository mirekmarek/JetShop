<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\NumberSeries;

trait Core_EShopEntity_HasNumberSeries_Trait
{
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50,
	)]
	protected string $number = '';
	
	public function getNumber() : string
	{
		return $this->number;
	}
	
	public function setNumber( string $number ) : void
	{
		$this->number = $number;
	}
	
	public static function getNumberSeriesEntityType(): string
	{
		return static::getEntityType();
	}
	
	public function getNumberSeriesEntityId(): int
	{
		return $this->getId();
	}
	
	public function getNumberSeriesEntityData(): ?Data_DateTime
	{
		return $this->created;
	}
	
	public function generateNumber() : void
	{
		if(!$this->number) {
			$this->number = NumberSeries::getManager()->generateNumber( $this );
			
			static::updateData(
				[
					'number'=>$this->number
				],
				[
					'id'=>$this->getId()
				]);
		}
	}
	
}
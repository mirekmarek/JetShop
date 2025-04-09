<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\DataModel_Related_1toN;

use JetApplication\Carrier_DeliveryPoint;

/**
 *
 */
#[DataModel_Definition(
	name: 'delivery_points_opening_hours',
	database_table_name: 'delivery_points_opening_hours',
	parent_model_class: Carrier_DeliveryPoint::class,
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: [
		'id_property_name' => 'id'
	]
)]
abstract class Core_Carrier_DeliveryPoint_OpeningHours extends DataModel_Related_1toN
{

	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
	)]
	protected int $id = 0;

	#[DataModel_Definition(
		related_to: 'main.id',
		is_key: true,
	)]
	protected int $place_id = 0;


	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $day = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $open1 = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $close1 = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $open2 = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $close2 = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $open3 = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $close3 = '';

	public function getArrayKeyValue(): string
	{
		return $this->id;
	}
	
	public function getId() : int
	{
		return $this->id;
	}

	public function setPlaceId( int $value ) : void
	{
		$this->place_id = $value;
	}

	public function getPlaceId() : int
	{
		return $this->place_id;
	}

	public function setDay( string $value ) : void
	{
		$this->day = $value;
	}

	public function getDay() : string
	{
		return $this->day;
	}

	public function setOpen1( string $value ) : void
	{
		$this->open1 = $value;
	}
	
	public function getOpen1() : string
	{
		return $this->open1;
	}


	public function setClose1( string $value ) : void
	{
		$this->close1 = $value;
	}
	
	public function getClose1() : string
	{
		return $this->close1;
	}
	
	public function setOpen2( string $value ) : void
	{
		$this->open2 = $value;
	}

	public function getOpen2() : string
	{
		return $this->open2;
	}
	
	public function setClose2( string $value ) : void
	{
		$this->close2 = $value;
	}

	public function getClose2() : string
	{
		return $this->close2;
	}

	public function setOpen3( string $value ) : void
	{
		$this->open3 = $value;
	}

	public function getOpen3() : string
	{
		return $this->open3;
	}

	public function setClose3( string $value ) : void
	{
		$this->close3 = $value;
	}
	
	public function getClose3() : string
	{
		return $this->close3;
	}
	
	public function specified() : bool
	{
		return
			trim($this->open1) ||
			trim($this->close1) ||
			trim($this->open2) ||
			trim($this->close2) ||
			trim($this->open3) ||
			trim($this->close3);
	}

	public function getHash() : string
	{
		$hash = '';

		foreach(get_object_vars($this) as $k=>$v) {
			if(
				$k[0]=='_' ||
				$k=='id' ||
				$k=='place_id'
			) {
				continue;
			}

			$hash .= ':'.$v;
		}

		return md5( $hash );
	}

}

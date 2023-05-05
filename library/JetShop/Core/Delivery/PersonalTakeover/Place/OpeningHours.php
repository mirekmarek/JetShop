<?php
/**
 * 
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\DataModel_Related_1toN;

use JetApplication\Delivery_PersonalTakeover_Place;

/**
 *
 */
#[DataModel_Definition(
	name: 'delivery_personal_takeover_place_opening_hours',
	database_table_name: 'delivery_personal_takeover_places_opening_hours',
	parent_model_class: Delivery_PersonalTakeover_Place::class,
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: [
		'id_property_name' => 'id'
	]
)]
abstract class Core_Delivery_PersonalTakeover_Place_OpeningHours extends DataModel_Related_1toN
{

	/**
	 * @var int
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
	)]
	protected int $id = 0;

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		related_to: 'main.id',
		is_key: true,
	)]
	protected string $place_id = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $day = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $open1 = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $close1 = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $open2 = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $close2 = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $open3 = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $close3 = '';

	public function getArrayKeyValue(): string
	{
		return $this->id;
	}

	/**
	 * @return int
	 */
	public function getId() : int
	{
		return $this->id;
	}

	/**
	 * @param string $value
	 */
	public function setPlaceId( string $value ) : void
	{
		$this->place_id = $value;
		
		if( $this->getIsSaved() ) {
			$this->setIsNew();
		}
		
	}

	/**
	 * @return string
	 */
	public function getPlaceId() : string
	{
		return $this->place_id;
	}

	/**
	 * @param string $value
	 */
	public function setDay( string $value ) : void
	{
		$this->day = $value;
	}

	/**
	 * @return string
	 */
	public function getDay() : string
	{
		return $this->day;
	}

	/**
	 * @param string $value
	 */
	public function setOpen1( string $value ) : void
	{
		$this->open1 = $value;
	}

	/**
	 * @return string
	 */
	public function getOpen1() : string
	{
		return $this->open1;
	}

	/**
	 * @param string $value
	 */
	public function setClose1( string $value ) : void
	{
		$this->close1 = $value;
	}

	/**
	 * @return string
	 */
	public function getClose1() : string
	{
		return $this->close1;
	}

	/**
	 * @param string $value
	 */
	public function setOpen2( string $value ) : void
	{
		$this->open2 = $value;
	}

	/**
	 * @return string
	 */
	public function getOpen2() : string
	{
		return $this->open2;
	}

	/**
	 * @param string $value
	 */
	public function setClose2( string $value ) : void
	{
		$this->close2 = $value;
	}

	/**
	 * @return string
	 */
	public function getClose2() : string
	{
		return $this->close2;
	}

	/**
	 * @param string $value
	 */
	public function setOpen3( string $value ) : void
	{
		$this->open3 = $value;
	}

	/**
	 * @return string
	 */
	public function getOpen3() : string
	{
		return $this->open3;
	}

	/**
	 * @param string $value
	 */
	public function setClose3( string $value ) : void
	{
		$this->close3 = $value;
	}

	/**
	 * @return string
	 */
	public function getClose3() : string
	{
		return $this->close3;
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

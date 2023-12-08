<?php
/**
 * 
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Form;

use JetApplication\Entity_WithShopRelation;
use JetApplication\Shops;
use JetApplication\Shops_Shop;
use JetApplication\Delivery_PersonalTakeover_Place_OpeningHours;
use JetApplication\Delivery_PersonalTakeover_Place;
use JetApplication\Delivery_Method;


/**
 *
 */
#[DataModel_Definition(
	name: 'delivery_personal_takeover_place',
	database_table_name: 'delivery_personal_takeover_places',
)]
class Core_Delivery_PersonalTakeover_Place extends Entity_WithShopRelation
{
	/**
	 * @var ?Form
	 */
	protected ?Form $_form_edit = null;

	/**
	 * @var ?Form
	 */
	protected ?Form $_form_add = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
	)]
	protected int $id = 0;

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 100,
	)]
	protected string $method_code = '';

	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 100,
	)]
	protected string $place_code = '';

	/**
	 * @var bool
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true,
	)]
	protected bool $is_active = true;

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $name = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $street = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $town = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $zip = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $latitude = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $longitude = '';

	/**
	 * @var mixed
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_CUSTOM_DATA,
	)]
	protected mixed $images = [];

	/**
	 * @var Delivery_PersonalTakeover_Place_OpeningHours[]
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Delivery_PersonalTakeover_Place_OpeningHours::class
	)]
	protected array $opening_hours = [];


	/**
	 * @param string $value
	 */
	public function setPlaceCode( string $value ) : void
	{
		$this->place_code = $value;
		
		if( $this->getIsSaved() ) {
			$this->setIsNew();
		}
		
	}

	/**
	 * @return string
	 */
	public function getPlaceCode() : string
	{
		return $this->place_code;
	}

	/**
	 * @return bool
	 */
	public function isActive(): bool
	{
		return $this->is_active;
	}

	/**
	 * @param bool $is_active
	 */
	public function setIsActive( bool $is_active ): void
	{
		$this->is_active = $is_active;
	}

	/**
	 * @return Form
	 */
	public function getEditForm() : Form
	{
		if(!$this->_form_edit) {
			$this->_form_edit = $this->createForm('edit_form');
		}
		
		return $this->_form_edit;
	}

	public function getKey() : string
	{
		return $this->method_code.':'.$this->place_code;
	}

	public function getHash() : string
	{
		$hash = '';

		foreach(get_object_vars($this) as $k=>$v) {
			if(
				$k[0]=='_' ||
				$k=='opening_hours' ||
				$k=='id'
			) {
				continue;
			}

			if(is_array($v)) {
				$v = implode(',', $v);
			}

			$hash .= ':'.$v;
		}

		foreach($this->opening_hours as $oh ) {
			$hash .= $oh->getHash();
		}

		return md5( $hash );
	}

	/**
	 * @return bool
	 */
	public function catchEditForm() : bool
	{
		return $this->getEditForm()->catch();
	}

	/**
	 * @return Form
	 */
	public function getAddForm() : Form
	{
		if(!$this->_form_add) {
			$this->_form_add = $this->createForm('add_form');
		}
		
		return $this->_form_add;
	}

	/**
	 * @return bool
	 */
	public function catchAddForm() : bool
	{
		return $this->getAddForm()->catch();
	}

	/**
	 * @param int|string $id
	 * @return static|null
	 */
	public static function get( int|string $id ) : static|null
	{
		return static::load( $id );
	}

	public static function getPlace( Shops_Shop $shop, string $method_code, string $place_code ) : static|null
	{
		/**
		 * @var Delivery_PersonalTakeover_Place[] $list
		 */
		$list = static::fetch([
			'delivery_personal_takeover_place' => [
				$shop->getWhere(),
				'AND',
				'method_code' => $method_code,
				'AND',
				'place_code' => $place_code
			]
		]);

		if(!count($list)) {
			return null;
		}

		return $list[0];
	}

	/**
	 * @return Delivery_PersonalTakeover_Place[]
	 */
	public static function getList() : iterable
	{
		$where = [];
		
		$list = static::fetchInstances( $where );
		
		return $list;
	}

	/**
	 * @param Shops_Shop $shop
	 * @param bool $only_active
	 * @param string $only_method_code
	 *
	 * @return Delivery_PersonalTakeover_Place[]
	 */
	public static function getListForShop( Shops_Shop $shop, bool $only_active=true, string $only_method_code='' ) : iterable
	{
		$where = $shop->getWhere();

		if($only_active) {
			$where[] = 'AND';
			$where['is_active'] = true;
		}

		if($only_method_code) {
			$where[] = 'AND';
			$where['method_code'] = $only_method_code;
		}

		$places = Delivery_PersonalTakeover_Place::fetch(['delivery_personal_takeover_place' => $where]);


		$list = [];
		foreach($places as $place) {
			/**
			 * @var Delivery_PersonalTakeover_Place $place
			 */
			$list[$place->getKey()] = $place;
		}

		return $list;
	}

	public static function getMapData( ?Shops_Shop $shop=null, array $only_method_codes = [] ) : array
	{
		if(!$shop) {
			$shop = Shops::getCurrent();
		}

		$select = [
			'method_code',
			'place_code',
			'latitude',
			'longitude'
		];

		$all_methods = Delivery_Method::getList();
		$active_methods = [];

		foreach( $all_methods as $method ) {
			if(
				$method->isPersonalTakeover() &&
				$method->getShopData($shop)->isActive()
			) {
				$active_methods[] = $method->getCode();
			}
		}

		if(!$only_method_codes) {
			$only_method_codes = $active_methods;
		} else {
			$only_method_codes = array_intersect($only_method_codes, $active_methods);
		}

		$where = [
			$shop->getWhere(),
			'AND',
			'locale' => $shop->getLocale(),
			'AND',
			'is_active' => true
		];

		if($only_method_codes) {
			$where[] = 'AND';
			$where['method_code'] = $only_method_codes;
		}

		$places = Delivery_PersonalTakeover_Place::dataFetchAll(
			select: $select,
			where: $where
		);

		$map_data = [];

		foreach($places as $place ) {
			$id = $place['method_code'].':'.$place['place_code'];

			$latitude = (float)$place['latitude'];
			$longitude = (float)$place['longitude'];

			$map_data[$id] = [
				'id' => $id,
				'latitude' => $latitude,
				'longitude' => $longitude,
			];
		}

		return $map_data;
	}


	/**
	 * @param string $value
	 */
	public function setMethodCode( string $value ) : void
	{
		$this->method_code = $value;
	}

	/**
	 * @return string
	 */
	public function getMethodCode() : string
	{
		return $this->method_code;
	}

	/**
	 * @param string $value
	 */
	public function setName( string $value ) : void
	{
		$this->name = $value;
	}

	/**
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * @param string $value
	 */
	public function setStreet( string $value ) : void
	{
		$this->street = $value;
	}

	/**
	 * @return string
	 */
	public function getStreet() : string
	{
		return $this->street;
	}

	/**
	 * @param string $value
	 */
	public function setTown( string $value ) : void
	{
		$this->town = $value;
	}

	/**
	 * @return string
	 */
	public function getTown() : string
	{
		return $this->town;
	}

	/**
	 * @param string $value
	 */
	public function setZip( string $value ) : void
	{
		$this->zip = $value;
	}

	/**
	 * @return string
	 */
	public function getZip() : string
	{
		return $this->zip;
	}

	/**
	 * @param string $value
	 */
	public function setLatitude( string $value ) : void
	{
		$this->latitude = $value;
	}

	/**
	 * @return string
	 */
	public function getLatitude() : string
	{
		return $this->latitude;
	}

	/**
	 * @param string $value
	 */
	public function setLongitude( string $value ) : void
	{
		$this->longitude = $value;
	}

	/**
	 * @return string
	 */
	public function getLongitude() : string
	{
		return $this->longitude;
	}


	public function addImage( string $image ) : void
	{
		$this->images[] = $image;
	}

	/**
	 * @return array
	 */
	public function getImages() : array
	{
		return $this->images;
	}


	public function addOpeningHours( string $day,

	                                 string $open1,
	                                 string $close1,

	                                 string $open2='',
	                                 string $close2='',

	                                 string $open3='',
	                                 string $close3='' ) : void
	{
		$oh = new Delivery_PersonalTakeover_Place_OpeningHours();
		$oh->setDay( $day );
		
		$oh->setOpen1( $open1 );
		$oh->setClose1( $close1 );

		$oh->setOpen2( $open2 );
		$oh->setClose2( $close2 );

		$oh->setOpen3( $open3 );
		$oh->setClose3( $close3 );

		$this->opening_hours[] = $oh;
	}

	/**
	 * @return Delivery_PersonalTakeover_Place_OpeningHours[]
	 */
	public function getOpeningHours() : iterable
	{
		return $this->opening_hours;
	}
}

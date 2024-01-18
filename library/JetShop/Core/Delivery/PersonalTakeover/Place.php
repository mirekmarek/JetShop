<?php
/**
 * 
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Delivery_Method_ShopData;
use JetApplication\Entity_WithShopRelation;
use JetApplication\Shops;
use JetApplication\Shops_Shop;
use JetApplication\Delivery_PersonalTakeover_Place_OpeningHours;
use JetApplication\Delivery_PersonalTakeover_Place;



/**
 *
 */
#[DataModel_Definition(
	name: 'delivery_personal_takeover_place',
	database_table_name: 'delivery_personal_takeover_places',
)]
class Core_Delivery_PersonalTakeover_Place extends Entity_WithShopRelation
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
	)]
	protected int $id = 0;


	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $method_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 100,
	)]
	protected string $hash = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 100,
	)]
	protected string $place_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true,
	)]
	protected bool $is_active = true;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $name = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $street = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $town = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $zip = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $country = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $latitude = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $longitude = '';
	
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

	
	public function setPlaceCode( string $value ) : void
	{
		$this->place_code = $value;
		
		if( $this->getIsSaved() ) {
			$this->setIsNew();
		}
		
	}

	public function getPlaceCode() : string
	{
		return $this->place_code;
	}
	
	public function isActive(): bool
	{
		return $this->is_active;
	}
	
	public function setIsActive( bool $is_active ): void
	{
		$this->is_active = $is_active;
	}


	public function getKey() : string
	{
		return $this->method_id.':'.$this->place_code;
	}

	public function generateHash() : string
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
		
		$this->hash = md5( $hash );

		return $this->hash;
	}


	public static function get( int $id ) : ?static
	{
		return static::load( $id );
	}

	public static function getPlace( Delivery_Method_ShopData $method, string $place_code ) : static|null
	{
		/**
		 * @var Delivery_PersonalTakeover_Place[] $list
		 */
		$list = static::fetch([
			'delivery_personal_takeover_place' => [
				$method->getShop()->getWhere(),
				'AND',
				'method_id' => $method->getId(),
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
	 * @param Delivery_Method_ShopData $method
	 * @param bool $only_active
	 *
	 * @return Delivery_PersonalTakeover_Place[]
	 */
	public static function getListForMethod( Delivery_Method_ShopData $method, bool $only_active=false ) : iterable
	{
		$where = $method->getShop()->getWhere();
		$where[] = 'AND';
		$where['method_id'] = $method->getId();
		
		if($only_active) {
			$where[] = 'AND';
			$where['is_active'] = true;
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
	
	/**
	 * @param Delivery_Method_ShopData $method
	 *
	 * @return array
	 */
	public static function getHashMapForMethod( Delivery_Method_ShopData $method ) : array
	{
		$where = $method->getShop()->getWhere();
		$where[] = 'AND';
		$where['method_id'] = $method->getId();
		
		
		return Delivery_PersonalTakeover_Place::dataFetchPairs(
			select: ['hash', 'id'],
			where: $where,
		);
	}
	
	

	/**
	 * @param Shops_Shop $shop
	 * @param bool $only_active
	 *
	 * @return Delivery_PersonalTakeover_Place[]
	 */
	public static function getListForShop( Shops_Shop $shop, bool $only_active=true ) : iterable
	{
		$where = $shop->getWhere();

		if($only_active) {
			$where[] = 'AND';
			$where['is_active'] = true;
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

	public static function getMapData( ?Shops_Shop $shop=null, array $only_method_ids = [] ) : array
	{
		if(!$shop) {
			$shop = Shops::getCurrent();
		}

		$select = [
			'method_id',
			'place_code',
			'latitude',
			'longitude'
		];
		
		$all_methods = Delivery_Method_ShopData::fetchInstances( $shop->getWhere() );
		$active_methods = [];
		
		$icons = [];

		foreach( $all_methods as $method ) {
			if(
				$method->isPersonalTakeover() &&
				$method->isActive()
			) {
				$active_methods[] = $method->getId();
				$icons[$method->getId()] = $method->getIcon2ThumbnailUrl( 60, 60 )?:'';
			}
		}

		if(!$only_method_ids) {
			$only_method_ids = $active_methods;
		} else {
			$only_method_ids = array_intersect($only_method_ids, $active_methods);
		}

		$where = [
			$shop->getWhere(),
			'AND',
			'locale' => $shop->getLocale(),
			'AND',
			'is_active' => true
		];

		if($only_method_ids) {
			$where[] = 'AND';
			$where['method_id'] = $only_method_ids;
		}

		$places = Delivery_PersonalTakeover_Place::dataFetchAll(
			select: $select,
			where: $where,
			raw_mode: true
		);

		$map_data = [];

		foreach($places as $place ) {
			$id = $place['method_id'].':'.$place['place_code'];

			$latitude = (float)$place['latitude'];
			$longitude = (float)$place['longitude'];

			$map_data[$id] = [
				'id' => $id,
				'icon' => $icons[$place['method_id']]??'',
				'latitude' => $latitude,
				'longitude' => $longitude,
			];
		}

		return $map_data;
	}
	
	public function setMethodId( int $value ) : void
	{
		$this->method_id = $value;
	}

	public function getMethodId() : int
	{
		return $this->method_id;
	}

	public function setName( string $value ) : void
	{
		$this->name = $value;
	}

	public function getName() : string
	{
		return $this->name;
	}

	public function setStreet( string $value ) : void
	{
		$this->street = $value;
	}
	
	public function getStreet() : string
	{
		return $this->street;
	}
	
	public function setTown( string $value ) : void
	{
		$this->town = $value;
	}
	
	public function getTown() : string
	{
		return $this->town;
	}
	
	public function setZip( string $value ) : void
	{
		$this->zip = $value;
	}
	
	public function getZip() : string
	{
		return $this->zip;
	}
	
	public function getCountry(): string
	{
		return $this->country;
	}
	
	public function setCountry( string $country ): void
	{
		$this->country = $country;
	}
	
	public function setLatitude( string $value ) : void
	{
		$this->latitude = $value;
	}
	
	public function getLatitude() : string
	{
		return $this->latitude;
	}
	
	public function setLongitude( string $value ) : void
	{
		$this->longitude = $value;
	}
	
	public function getLongitude() : string
	{
		return $this->longitude;
	}


	public function addImage( string $image ) : void
	{
		$this->images[] = $image;
	}
	
	public function getImages() : array
	{
		$_images = [];
		foreach($this->images as $img) {
			if($img) {
				$_images[] = $img;
			}
		}
		return $_images;
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

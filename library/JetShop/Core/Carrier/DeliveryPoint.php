<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Data_Text;
use Jet\DataModel;
use Jet\DataModel_Definition;

use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use Jet\Form_Field_Float;
use Jet\Form_Field_Input;
use Jet\Locale;
use JetApplication\Carrier;
use JetApplication\Delivery_Method;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShops;
use JetApplication\EShop;
use JetApplication\Carrier_DeliveryPoint_OpeningHours;
use JetApplication\Carrier_DeliveryPoint;
use JetApplication\WarehouseManagement_Warehouse;


/**
 *
 */
#[DataModel_Definition(
	name: 'delivery_points',
	database_table_name: 'delivery_points',
)]
class Core_Carrier_DeliveryPoint extends EShopEntity_Basic implements Form_Definition_Interface
{
	use Form_Definition_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
	)]
	protected int $id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 100,
	)]
	protected string $carrier_code = '';
	
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
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Code:',
		is_required: true,
		error_messages: [
		]
	)]
	protected string $point_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 100,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Type:',
		error_messages: [
		]
	)]
	protected string $point_type = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_LOCALE,
		is_key: true
	)]
	protected ?Locale $point_locale = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Is active',
		default_value_getter_name: 'isActive',
		setter_name: 'setIsActive',
	)]
	protected bool $is_active = true;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Name:',
		is_required: true,
		error_messages: [
		]
	)]
	protected string $name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $name_search = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Address:',
		is_required: true,
		error_messages: [
		]
	)]
	protected string $street = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $street_search = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Town:',
		is_required: true,
		error_messages: [
		]
	)]
	protected string $town = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $town_search = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'ZIP:',
		is_required: true,
		error_messages: [
		]
	)]
	protected string $zip = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $zip_search = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Country:',
		is_required: true,
		error_messages: [
		]
	)]
	protected string $country = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Latitude:',
		is_required: true,
		error_messages: [
		]
	)]
	protected string $latitude = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Longitude:',
		is_required: true,
		error_messages: [
		]
	)]
	protected string $longitude = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_CUSTOM_DATA,
	)]
	protected mixed $images = [];

	/**
	 * @var Carrier_DeliveryPoint_OpeningHours[]
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Carrier_DeliveryPoint_OpeningHours::class
	)]
	protected array $opening_hours = [];
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Dedicated warehouse:',
		select_options_creator: [
			WarehouseManagement_Warehouse::class,
			'getScope'
		],
	)]
	protected int $dedicatet_warehouse_id = 0;
	
	protected ?Form $add_form = null;
	protected ?Form $edit_form = null;

	
	public function setPointCode( string $value ) : void
	{
		$this->point_code = $value;
	}

	public function getPointCode() : string
	{
		return $this->point_code;
	}
	
	public function getPointType(): string
	{
		return $this->point_type;
	}
	
	public function setPointType( string $point_type ): void
	{
		$this->point_type = $point_type;
	}
	

	public function getPointLocale(): ?Locale
	{
		return $this->point_locale;
	}
	
	public function setPointLocale( Locale $point_locale ): void
	{
		$this->point_locale = $point_locale;
		$this->country = $point_locale->getRegion();
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
		return $this->carrier_code.':'.$this->point_code;
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


	public static function get( int|string $id ) : ?static
	{
		return static::load( $id );
	}

	public static function getPoint( Carrier $carrier, string $code ) : static|null
	{
		/**
		 * @var Carrier_DeliveryPoint[] $list
		 */
		$list = static::fetch([
			'' => [
				'carrier_code' => $carrier->getCode(),
				'AND',
				'point_code' => $code
			]
		]);

		if(!count($list)) {
			return null;
		}

		return $list[0];
	}
	
	/**
	 * @param Carrier $carrier
	 * @param array|null $only_types
	 * @param Locale|null $only_locale
	 * @param bool $only_active
	 * @return Carrier_DeliveryPoint[]
	 */
	public static function getPointList( Carrier $carrier, ?array $only_types=null, ?Locale $only_locale=null, bool $only_active=false ) : iterable
	{
		$where['carrier_code'] = $carrier->getCode();
		
		if($only_locale) {
			$where[] = 'AND';
			$where['point_locale'] = $only_locale;
		}
		
		if($only_types) {
			$where[] = 'AND';
			$where['point_type'] = $only_types;
		}
		
		if($only_active) {
			$where[] = 'AND';
			$where['is_active'] = true;
		}
		
		
		$points = Carrier_DeliveryPoint::fetch(['' => $where]);
		
		
		$list = [];
		foreach($points as $point) {
			/**
			 * @var Carrier_DeliveryPoint $point
			 */
			$list[$point->getKey()] = $point;
		}
		
		return $list;
	}
	
	/**
	 * @param Core_Carrier $carrier
	 *
	 * @return array
	 */
	public static function getHashMap( Core_Carrier $carrier ) : array
	{
		$where['carrier_code'] = $carrier->getCode();
		
		
		return Carrier_DeliveryPoint::dataFetchPairs(
			select: ['hash', 'id'],
			where: $where,
		);
	}
	

	public static function getMapData( ?EShop $eshop=null, array $only_method_ids = [] ) : array
	{
		if(!$eshop) {
			$eshop = EShops::getCurrent();
		}

		$point_select = [
			'carrier_code',
			'point_code',
			'latitude',
			'longitude'
		];
		
		
		$map_data = [];
		
		$all_methods = Delivery_Method::fetchInstances( $eshop->getWhere() );
		
		foreach( $all_methods as $method ) {
			if(
				$method->isPersonalTakeover() &&
				$method->isActive() &&
				(
					!$only_method_ids ||
					in_array($method->getId(), $only_method_ids)
				)
			) {
				$points_where = [
					'is_active' => true,
					'AND',
					'carrier_code' => $method->getCarrierCode(),
					'AND',
					'point_locale' => $method->getLocale(),
				];
				
				if( $method->getAllowedDeliveryPointTypes() ) {
					$points_where[] = 'AND';
					$points_where['point_type'] = $method->getAllowedDeliveryPointTypes();
				}
				
				$_points = Carrier_DeliveryPoint::dataFetchAll(
					select: $point_select,
					where: $points_where,
					raw_mode: true
				);
				
				foreach($_points as $point ) {
					$map_data[] = [
						'm' => $method->getId(),
						'c' => $point['point_code'],
						'lt' => (float)$point['latitude'],
						'ln' => (float)$point['longitude'],
					];
				}
			}
		}
		




		return $map_data;
	}
	
	
	public function setCarrier( Carrier $carrier ) : void
	{
		$this->carrier_code = $carrier->getCode();
	}
	
	public function getCarrierCode() : string
	{
		return $this->carrier_code;
	}

	
	

	public function setName( string $value ) : void
	{
		$this->name = $value;
		$this->name_search = strtolower(Data_Text::removeAccents( $value ));
	}

	public function getName() : string
	{
		return $this->name;
	}

	public function setStreet( string $value ) : void
	{
		$this->street = $value;
		$this->street_search = strtolower(Data_Text::removeAccents( $value ));
	}
	
	public function getStreet() : string
	{
		return $this->street;
	}
	
	public function setTown( string $value ) : void
	{
		$this->town = $value;
		$this->town_search = strtolower(Data_Text::removeAccents( $value ));
	}
	
	public function getTown() : string
	{
		return $this->town;
	}
	
	public function setZip( string $value ) : void
	{
		$this->zip = $value;
		$this->zip_search = strtolower(Data_Text::removeAccents( $value ));
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
	
	public function getDedicatetWarehouseId(): int
	{
		return $this->dedicatet_warehouse_id;
	}
	
	public function setDedicatetWarehouseId( int $dedicatet_warehouse_id ): void
	{
		$this->dedicatet_warehouse_id = $dedicatet_warehouse_id;
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
		$oh = new Carrier_DeliveryPoint_OpeningHours();
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
	 * @return Carrier_DeliveryPoint_OpeningHours[]
	 */
	public function getOpeningHours() : iterable
	{
		return $this->opening_hours;
	}
	
	public function getOpeningHoursSpecified() : bool
	{
		if(!$this->opening_hours) {
			return false;
		}
		
		foreach($this->opening_hours as $oh) {
			if($oh->specified()) {
				return true;
			}
		}
		
		return false;
	}
	
	
	/**
	 * @param string $q
	 * @param Delivery_Method[] $only_methods
	 * @return static[]
	 */
	public static function search( string $q, array $only_methods ) : array
	{
		$result = [];
		foreach($only_methods as $method) {
			
			$only_where = [
				'carrier_code' => $method->getCarrier()->getCode(),
			];
			
			if( $method->getAllowedDeliveryPointTypes() ) {
				$only_where[] = 'AND';
				$only_where['point_type'] = $method->getAllowedDeliveryPointTypes();
			}
			
			
			$where = [
				'is_active' => true,
				'AND',
				$only_where
			];
			
			$_points = static::dataFetchAll(
				select: [
					'id',
					'name_search',
					'town_search',
					'street_search',
					'zip_search'
				],
				where: $where,
				raw_mode: true
			);
			
			$ids = [];
			
			$q = strtolower(Data_Text::removeAccents( $q ));
			foreach($_points as $place) {
				if(
					str_contains( $place['zip_search'], $q ) ||
					str_contains( $place['name_search'], $q ) ||
					str_contains( $place['town_search'], $q ) ||
					str_contains( $place['street_search'], $q )
				) {
					$ids[] = $place['id'];
				}
				
				if(count($ids)>=200) {
					break;
				}
			}
			
			if($ids) {
				$points = static::fetch([''=>[
					'id' => $ids
				]]);
				
				foreach($points as $point) {
					$result[] = [
						'point' => $point,
						'method' => $method
					];
				}
			}
		}
		
		return $result;
	}
	
	public function getAddForm() : Form
	{
		if(!$this->add_form) {
			$this->add_form = $this->createForm('add_form');
			
			$this->add_form->setNovalidate(true);
			
			/**
			 * @var Form_Field_Float $latitude
			 */
			$latitude = $this->add_form->getField('latitude');
			$latitude->setPlaces(16);
			$latitude->setStep(0.00000000000001);
			
			$code = $this->add_form->field('point_code');
			$code->setErrorMessages([
				Form_Field::ERROR_CODE_EMPTY => 'Please enter code',
				'not_unique' => 'This code is already used'
			]);
			$code->setValidator( function() use ($code) : bool {
				
				$exists = Carrier_DeliveryPoint::getPoint(
					Carrier::get( $this->carrier_code ),
					$code->getValue()
				);
				
				if($exists) {
					$code->setError('not_unique');
					return false;
				}
				
				return true;
			} );
			
			$this->updateForm(  $this->add_form);
			
			
		}
		
		return $this->add_form;
	}
	
	
	public function getEditForm() : Form
	{
		if(!$this->edit_form) {
			$this->edit_form = $this->createForm('edit_form');
			$this->edit_form->field('point_code')->setIsReadonly( true );
			$this->edit_form->setNovalidate(true);
			
			$this->updateForm( $this->edit_form );
		}
		
		return $this->edit_form;
	}
	
	protected function updateForm( Form $form ) : void
	{
		foreach($this->getOpeningHours() as $oh) {
			$day = $oh->getDay();
			
			$open1 = new Form_Field_Input('/'.$day.'/open_1', '');
			$open1->setDefaultValue( $oh->getOpen1() );
			$open1->setFieldValueCatcher( function( string $value ) use ($oh) {
				$oh->setOpen1( $value );
			} );
			$form->addField($open1);
			
			$close1 = new Form_Field_Input('/'.$day.'/close_1', '');
			$close1->setDefaultValue( $oh->getClose1() );
			$close1->setFieldValueCatcher( function( string $value ) use ($oh) {
				$oh->setClose1( $value );
			} );
			$form->addField($close1);
			
			
			$open2 = new Form_Field_Input('/'.$day.'/open_2', '');
			$open2->setDefaultValue( $oh->getOpen2() );
			$open2->setFieldValueCatcher( function( string $value ) use ($oh) {
				$oh->setOpen2( $value );
			} );
			$form->addField($open2);
			
			$close2 = new Form_Field_Input('/'.$day.'/close_2', '');
			$close2->setDefaultValue( $oh->getClose2() );
			$close2->setFieldValueCatcher( function( string $value ) use ($oh) {
				$oh->setClose2( $value );
			} );
			$form->addField($close2);
			
			
			$open3 = new Form_Field_Input('/'.$day.'/open_3', '');
			$open3->setDefaultValue( $oh->getOpen3() );
			$open3->setFieldValueCatcher( function( string $value ) use ($oh) {
				$oh->setOpen3( $value );
			} );
			$form->addField($open3);
			
			$close3 = new Form_Field_Input('/'.$day.'/close_3', '');
			$close3->setDefaultValue( $oh->getClose3() );
			$close3->setFieldValueCatcher( function( string $value ) use ($oh) {
				$oh->setClose3( $value );
			} );
			$form->addField($close3);
			
			
		}
		
	}
	
}

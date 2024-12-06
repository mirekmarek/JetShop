<?php
namespace JetShop;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_DateTime;
use Jet\Form_Field_Select;
use Jet\Locale;
use Jet\Tr;
use JetApplication\Entity_Basic;
use JetApplication\Entity_Marketing;
use JetApplication\Product_Relation;
use JetApplication\ProductFilter;
use JetApplication\EShop_Managers;
use JetApplication\EShops;
use JetApplication\EShop;

#[DataModel_Definition]
abstract class Core_Entity_Marketing extends Entity_Basic
{
	public const RELEVANCE_MODE_ALL = 'all';
	public const RELEVANCE_MODE_BY_FILTER = 'by_filter';
	public const RELEVANCE_MODE_ALL_BUT_FILTER = 'all_but_filter';
	public const RELEVANCE_MODE_ONLY_PRODUCTS = 'only_products';
	public const RELEVANCE_MODE_ALL_BUT_PRODUCTS = 'all_but_products';
	
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'e-shop',
		is_required: true,
		error_messages: [
			Form_Field::ERROR_CODE_INVALID_VALUE => 'Invalid value',
			Form_Field::ERROR_CODE_EMPTY         => 'Invalid value'
		],
		select_options_creator: [
			EShops::class,
			'getScope'
		],
		default_value_getter_name: 'getShopKey',
		creator: ['this', 'eshopFieldCreator']
	)]
	protected ?EShop $eshop = null;
	
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $eshop_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_LOCALE,
		is_key: true,
	)]
	protected ?Locale $locale = null;
	
	public function eshopFieldCreator( Form_Field $field ) : Form_Field
	{
		$field->setFieldValueCatcher( function( string $eshop_key ) {
			$eshop = EShops::get( $eshop_key );
			$this->setEshop( $eshop );
		} );
		
		return $field;
	}
	
	public function setEshop( EShop $eshop ) : void
	{
		$this->eshop_code = $eshop->getCode();
		$this->locale = $eshop->getLocale();
		$this->eshop = $eshop;
	}
	
	public function getEshopCode() : string
	{
		return $this->eshop_code;
	}
	
	
	public function getLocale(): ?Locale
	{
		return $this->locale;
	}
	
	public function getEshop() : EShop
	{
		if(!$this->eshop) {
			$this->eshop = EShops::get( $this->getShopKey() );
		}
		
		return $this->eshop;
	}
	
	public function getShopKey() : string
	{
		return $this->eshop_code.'_'.$this->locale;
	}
	
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Internal name:'
	)]
	protected string $internal_name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Internal code:',
		error_messages: [
			'code_used' => 'This code is already used'
		]
	)]
	protected string $internal_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Internal notes:'
	)]
	protected string $internal_notes = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Is active'
	)]
	protected bool $is_active = false;
	
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
	
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Relevance mode:',
		select_options_creator: [
			Entity_Marketing::class,
			'getRelevanceModeScope'
		],
		error_messages: [
			Form_Field_Select::ERROR_CODE_EMPTY => 'Invalid value',
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]
	)]
	protected string $relevance_mode = self::RELEVANCE_MODE_ALL;
	
	protected ?array $product_ids = null;
	protected ?ProductFilter $product_filter = null;
	
	
	public function getInternalName(): string
	{
		return $this->internal_name;
	}
	
	public function setInternalName( string $internal_name ): void
	{
		$this->internal_name = $internal_name;
	}
	
	public function getInternalCode(): string
	{
		return $this->internal_code;
	}
	
	public function setInternalCode( string $internal_code ): void
	{
		$this->internal_code = $internal_code;
	}
	
	public function getInternalNotes(): string
	{
		return $this->internal_notes;
	}
	
	
	public function setInternalNotes( string $internal_notes ): void
	{
		$this->internal_notes = $internal_notes;
	}
	
	
	public static function internalCodeUsed( string $internal_code, int $skip_id = 0 ): bool
	{
		return (bool)static::dataFetchCol( ['id'], [
			'internal_code' => $internal_code,
			'AND',
			'id !='         => $skip_id
		] );
	}
	
	
	public function getIsActive(): bool
	{
		return $this->is_active;
	}
	
	public function setIsActive( bool $is_active ): void
	{
		$this->is_active = $is_active;
	}
	
	
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
	
	public function getAdminTitle(): string
	{
		return $this->internal_name;
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
	
	
	public static function getRelevanceModeScope() : array
	{
		return [
			static::RELEVANCE_MODE_ALL              => Tr::_('All products'),
			static::RELEVANCE_MODE_BY_FILTER        => Tr::_('By filter'),
			static::RELEVANCE_MODE_ALL_BUT_FILTER   => Tr::_('All but filter'),
			static::RELEVANCE_MODE_ONLY_PRODUCTS    => Tr::_('Only products'),
			static::RELEVANCE_MODE_ALL_BUT_PRODUCTS => Tr::_('All but products'),
		];
	}
	
	
	public function getRelevanceMode(): string
	{
		return $this->relevance_mode;
	}
	
	public function setRelevanceMode( string $relevance_mode ): void
	{
		$this->relevance_mode = $relevance_mode;
	}
	
	
	
	
	public function getProductsFilter() : ProductFilter
	{
		if($this->product_filter===null) {
			$this->product_filter = new ProductFilter( $this->getEshop() );
			$this->product_filter->setContextEntity( static::getEntityType() );
			$this->product_filter->setContextEntityId( $this->id );
			$this->product_filter->load();
			
		}
		
		return $this->product_filter;
	}
	
	public function getProductIds() : array|bool
	{
		if($this->product_ids===null) {
			switch( $this->getRelevanceMode() ) {
				case static::RELEVANCE_MODE_ALL:
					$this->product_ids = [];
					break;
				case static::RELEVANCE_MODE_ONLY_PRODUCTS:
				case static::RELEVANCE_MODE_ALL_BUT_PRODUCTS:
					$this->product_ids = Product_Relation::get( $this );
					break;
				case static::RELEVANCE_MODE_BY_FILTER:
				case static::RELEVANCE_MODE_ALL_BUT_FILTER:
					$this->product_ids = $this->getProductsFilter()->filter();
					break;
				
			}
		}
		
		return $this->product_ids;
	}
	
	
	public function addProduct( int $product_id ) : bool
	{
		$ids = $this->getProductIds();
		if(in_array($product_id, $ids)) {
			return false;
		}
		
		Product_Relation::add( $this, $product_id );
		
		$this->product_ids[] = $product_id;
		
		return true;
	}
	
	public function removeProduct( int $product_id ) : bool
	{
		Product_Relation::remove( $this, $product_id );
		
		$this->product_ids = null;
		
		return true;
	}
	
	public function removeAllProducts() : bool
	{
		Product_Relation::removeAll( $this );
		
		$this->product_ids = null;
		
		return true;
	}
	
	public function isRelevant( array $product_ids ) : bool
	{
		if($this->relevance_mode==static::RELEVANCE_MODE_ALL) {
			return true;
		}

		
		$match = (bool)array_intersect($this->getProductIds(), $product_ids);
		
		if(
			$this->relevance_mode==static::RELEVANCE_MODE_ALL_BUT_FILTER ||
			$this->relevance_mode==static::RELEVANCE_MODE_ALL_BUT_PRODUCTS
		) {
			$match = !$match;
		}

		return $match;
	}
	
	public function getImage( string $image_class ) : string
	{
		return $this->{"image_{$image_class}"};
	}
	
	public function setImage( string $image_class, $image ) : void
	{
		$this->{"image_{$image_class}"} = $image;
	}
	
	public function getImageThumbnailUrl( string $image_class, int $max_w, int $max_h ): string
	{
		return EShop_Managers::Image()->getThumbnailUrl(
			$this->getImage( $image_class ),
			$max_w,
			$max_h
		);
	}
	
	public function getImageUrl( string $image_class ): string
	{
		return EShop_Managers::Image()->getUrl(
			$this->getImage( $image_class )
		);
	}
	
	
	
	
	
	public static function getByInternalCode( string $internal_code, ?EShop $eshop=null ) : ?static
	{
		$where = $eshop->getWhere();
		$where[] = 'AND';
		$where['internal_code'] = $internal_code;
		
		return static::load( $where );
	}
	
	public static function getActiveByInternalCode( string $internal_code, ?EShop $eshop=null ) : ?static
	{
		$where = static::getActiveQueryWhere( $eshop );
		$where[] = 'AND';
		$where['internal_code'] = $internal_code;
		
		return static::load( $where );
	}
	
	
	/**
	 * @param array $ids
	 * @param EShop|null $eshop
	 * @param array|string|null $order_by
	 * @return static[]
	 */
	public static function getActiveList( array $ids, ?EShop $eshop=null, array|string|null $order_by = null ) : array
	{
		if(!$ids) {
			return [];
		}
		
		$where = static::getActiveQueryWhere( $eshop );
		$where[] = 'AND';
		$where['entity_id'] = $ids;
		
		$_res =  static::fetch(
			where_per_model: [ ''=>$where],
			order_by: $order_by,
			item_key_generator: function( Entity_Marketing $item ) : int {
				return $item->getId();
			}
		);
		
		if($order_by) {
			return $_res;
		}
		
		$res = [];
		
		foreach($ids as $id) {
			if(isset($_res[$id])) {
				$res[$id] = $_res[$id];
			}
		}
		
		return $res;
	}
	
	/**
	 * @param EShop|null $eshop
	 * @param array|string|null $order_by
	 * @return static[]
	 */
	public static function getAllActive( ?EShop $eshop=null, array|string|null $order_by = null ) : array
	{
		$where = static::getActiveQueryWhere( $eshop );
		
		return static::fetch(
			where_per_model: [ ''=>$where],
			order_by: $order_by,
			item_key_generator: function( Entity_Marketing $item ) : int {
				return $item->getId();
			}
		);
	}
	
	
	public static function getActiveQueryWhere( ?EShop $eshop=null ) : array
	{
		$eshop = $eshop?:EShops::getCurrent();
		
		$where = [];
		$where[] = $eshop->getWhere();
		$where[] = 'AND';
		$where[] = [
			'is_active' => true
		];
		
		return $where;
	}
	
	
	public static function getNonActiveQueryWhere( ?EShop $eshop=null ) : array
	{
		$eshop = $eshop?:EShops::getCurrent();
		
		$where = [];
		$where[] = $eshop->getWhere();
		$where[] = 'AND';
		$where[] = [
			'is_active' => false
		];
		
		return $where;
	}
	
	
}
<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Closure;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_Definition_Model_Related_1toN;
use Jet\DataModel_IDController_Passive;
use Jet\DataModel_Related_1toN;
use Jet\Form;
use Jet\Locale;
use Jet\MVC_Cache;
use JetApplication\EShopEntity_HasEShopRelation_Interface;
use JetApplication\EShopEntity_HasEShopRelation_Trait;
use JetApplication\EShopEntity_WithEShopData;
use JetApplication\EShopEntity_WithEShopData_EShopData;
use JetApplication\EShops;
use JetApplication\EShop;

#[DataModel_Definition(
	database_table_name: '',
	id_controller_class: DataModel_IDController_Passive::class,
	parent_model_class: EShopEntity_WithEShopData::class
)]
abstract class Core_EShopEntity_WithEShopData_EShopData extends DataModel_Related_1toN implements
	EShopEntity_HasEShopRelation_Interface
{
	use EShopEntity_HasEShopRelation_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
		is_id: true
	)]
	protected string $eshop_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_LOCALE,
		is_key: true,
		is_id: true
	)]
	protected ?Locale $locale = null;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'main.id',
	)]
	protected int $entity_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true,
	)]
	protected bool $entity_is_active = true;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $internal_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true,
	)]
	protected bool $is_active_for_eshop = false;
	
	protected static array $loaded_items = [];
	
	public static function getEntityType() : string
	{
		
		$def = static::getDataModelDefinition(static::class);
		/**
		 * @var DataModel_Definition_Model_Related_1toN $def
		 */
		return $def->getParentModelDefinition()->getModelName();
	}
	
	
	public static function get( int $id, ?EShop $eshop=null ) : ?static
	{
		$eshop = $eshop?:EShops::getCurrent();
		
		$key = get_called_class().':'.$eshop->getKey().':'.$id;
		
		if(!array_key_exists($key, static::$loaded_items)) {
			$where = $eshop->getWhere();
			$where[] = 'AND';
			$where['entity_id'] = $id;
			
			static::$loaded_items[ $key ] = static::load( $where );
		}
		
		
		return static::$loaded_items[ $key ];
	}
	
	public static function getByInternalCode( string $internal_code, ?EShop $eshop=null ) : ?static
	{
		$eshop = $eshop ? : EShops::getCurrent();
		
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
			item_key_generator: function( EShopEntity_WithEShopData_EShopData $item ) : int {
				return $item->getEntityId();
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
			item_key_generator: function( EShopEntity_WithEShopData_EShopData $item ) : int {
				return $item->getEntityId();
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
			'entity_is_active' => true,
			'AND',
			'is_active_for_eshop' => true
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
			'entity_is_active' => false,
			'OR',
			'is_active_for_eshop' => false
		];
		
		return $where;
	}
	
	public function getId() : int
	{
		return $this->getEntityId();
	}

	public function getEntityId(): int
	{
		return $this->entity_id;
	}
	
	public function setEntityId( int $entity_id ): void
	{
		$this->entity_id = $entity_id;
	}
	
	
	
	public function getLocale(): Locale
	{
		return $this->locale;
	}
	
	
	
	public function getArrayKeyValue() : string
	{
		return $this->eshop_code.'_'.$this->locale;
	}
	

	public function getInternalCode(): string
	{
		return $this->internal_code;
	}
	
	public function setInternalCode( string $internal_code ): void
	{
		$this->internal_code = $internal_code;
	}
	
	public function isEntityActive(): bool
	{
		return $this->entity_is_active;
	}
	
	public function _setEntityIsActive( bool $entity_is_active ): void
	{
		$this->entity_is_active = $entity_is_active;
		
		static::updateData(
			data: [
				'entity_is_active' => $this->entity_is_active
			],
			where: [
				'entity_id' => $this->entity_id
			]
		);
	}
	
	
	public function isActive() : bool
	{
		return $this->is_active_for_eshop && $this->entity_is_active;
	}
	
	public function isActiveForShop(): bool
	{
		return $this->is_active_for_eshop;
	}
	
	public function _activate() : void
	{
		$this->is_active_for_eshop = true;
		$where = $this->getEshop()->getWhere();
		$where[] = 'AND';
		$where['entity_id'] = $this->entity_id;
		
		static::updateData(
			data: [
				'is_active_for_eshop' => $this->is_active_for_eshop
			],
			where: $where
		);
		
		MVC_Cache::resetOutputCache();
	}
	
	public function _deactivate() : void
	{
		$this->is_active_for_eshop = false;
		$where = $this->getEshop()->getWhere();
		$where[] = 'AND';
		$where['entity_id'] = $this->entity_id;
		
		static::updateData(
			data: [
				'is_active_for_eshop' => $this->is_active_for_eshop
			],
			where: $where
		);
		
		MVC_Cache::resetOutputCache();
	}
	
	
	
	public function createForm( string $form_name, array $only_fields=[], array $exclude_fields=[]   ) : Form
	{
		if(!EShops::exists( $this->getEshopKey() )) {
			return new Form($form_name, []);
		}
		
		return parent::createForm( $form_name, $only_fields, $exclude_fields );
	}
	
	public static function cloneAllEShopData( EShop $source_eshop, EShop $target_eshop, ?Closure $verboser=null ) : void
	{
		$ids = static::dataFetchCol( select: ['entity_id'], where: $source_eshop->getWhere() );
		$count = count($ids);
		$c = 0;
		foreach( $ids as $id ) {
			$c++;
			if($verboser) {
				$verboser( $c, $count, $id );
			}
			
			static::cloneEShopData( $id, $source_eshop, $target_eshop );
		}
	}
	
	public static function cloneEShopData( int $entity_id, EShop $source_eshop, EShop $target_eshop ) : ?static
	{
		$item = static::load([
			'entity_id' => $entity_id,
			'AND',
			$source_eshop->getWhere()
		]);
		if(!$item) {
			return null;
		}
		
		$item->setEshop( $target_eshop );
		
		$exists = static::dataFetchOne( select: ['entity_id'], where: [
			$target_eshop->getWhere(),
			'AND',
			'entity_id' => $entity_id
		] );
		if(!$exists) {
			$item->setIsNew( true );
		}
		
		$item->save();
		
		return $item;
	}
	
}
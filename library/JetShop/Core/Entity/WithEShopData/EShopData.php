<?php
namespace JetShop;

use Jet\Data_Text;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_Definition_Model_Related_1toN;
use Jet\DataModel_IDController_Passive;
use Jet\DataModel_Related_1toN;
use Jet\Form;
use Jet\Locale;
use Jet\Tr;
use JetApplication\Entity_WithEShopData;
use JetApplication\Entity_WithEShopData_EShopData;
use JetApplication\EShop_Managers;
use JetApplication\EShops;
use JetApplication\EShop;
use JetApplication\Timer_Action;

#[DataModel_Definition(
	database_table_name: '',
	id_controller_class: DataModel_IDController_Passive::class,
	parent_model_class: Entity_WithEShopData::class
)]
abstract class Core_Entity_WithEShopData_EShopData extends DataModel_Related_1toN
{
	protected ?EShop $_eshop = null;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'main.id',
	)]
	protected int $entity_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
		is_id: true,
	)]
	protected string $eshop_code = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_LOCALE,
		is_key: true,
		is_id: true,
	)]
	protected ?Locale $locale = null;
	
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
			item_key_generator: function( Entity_WithEShopData_EShopData $item ) : int {
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
			item_key_generator: function( Entity_WithEShopData_EShopData $item ) : int {
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
	
	
	
	public function setEshop( EShop $eshop ) : void
	{
		$this->eshop_code = $eshop->getCode();
		$this->locale = $eshop->getLocale();
		$this->_eshop = $eshop;
	}
	
	public function getEshopCode() : string
	{
		return $this->eshop_code;
	}
	
	public function getEshop() : EShop
	{
		if(!$this->_eshop) {
			$this->_eshop = EShops::get( $this->getEshopKey() );
		}
		
		return $this->_eshop;
	}
	
	public function getEshopKey() : string
	{
		return $this->eshop_code.'_'.$this->locale;
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
	}
	
	
	
	public function createForm( string $form_name, array $only_fields=[], array $exclude_fields=[]   ) : Form
	{
		if(!EShops::exists( $this->getEshopKey() )) {
			return new Form($form_name, []);
		}
		
		return parent::createForm( $form_name, $only_fields, $exclude_fields );
	}
	
	public function _generateURLPathPart( string $name, $type ) : string
	{
		$name = Data_Text::removeAccents( $name );
		
		$name = strtolower($name);
		$name = preg_replace('/([^0-9a-zA-Z ])+/', '', $name);
		$name = preg_replace( '/([[:blank:]])+/', '-', $name);
		
		
		$min_len = 2;
		
		$parts = explode('-', $name);
		$valid_parts = array();
		foreach( $parts as $value ) {
			
			if (strlen($value) > $min_len) {
				$valid_parts[] = $value;
			}
		}
		
		$name = count($valid_parts) > 1 ? implode('-', $valid_parts) : $name;
		
		return $name.'-'.$type.'-'.$this->getEntityId();
	}
	
	public function _generateURLParam( string $name,string $url_param_property='url_param' ) : string
	{
		$name = Data_Text::removeAccents( $name );
		
		$name = strtolower($name);
		$name = preg_replace('/([^0-9a-zA-Z ])+/', '', $name);
		$name = preg_replace( '/([[:blank:]])+/', '-', $name);
		
		
		$min_len = 2;
		
		$parts = explode('-', $name);
		$valid_parts = array();
		foreach( $parts as $value ) {
			
			if (strlen($value) > $min_len) {
				$valid_parts[] = $value;
			}
		}
		
		$url_param_base = count($valid_parts) > 1 ? implode('-', $valid_parts) : $name;
		$url_param = $url_param_base;
		
		$exists = function() use (&$url_param, $url_param_base, $url_param_property) : bool
		{
			$where = $this->getEshop()->getWhere();
			$where[] = 'AND';
			$where[$url_param_property]=$url_param;
			$where[] = 'AND';
			$where['entity_id !='] = $this->entity_id;
			
			return (bool)count(static::dataFetchCol(['entity_id'], $where));
		};
		
		$suffix = 0;
		while($exists()) {
			$suffix++;
			$url_param = $url_param_base.$suffix;
		}
		
		return $url_param;
		
		
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
	
	
	/**
	 * @return Timer_Action[]
	 */
	public function getAvailableTimerActions() : array
	{
		$eshop = $this->getEshop();
		
		$activate = new class( $eshop ) extends Timer_Action {
			public function __construct( EShop $eshop )
			{
				$this->setEshop( $eshop );
			}
			
			public function getKey(): string
			{
				return 'activate';
			}
			
			public function getTitle(): string
			{
				return Tr::_('Activate');
			}
			
			public function perform( Entity_WithEShopData $entity, mixed $action_context ): bool
			{
				$entity->activateEShopData( $this->eshop );
				return true;
			}
		};
		
		$deactivate = new class($eshop) extends Timer_Action {
			public function __construct( EShop $eshop )
			{
				$this->setEshop( $eshop );
			}
			
			public function getKey(): string
			{
				return 'deactivate';
			}
			
			public function getTitle(): string
			{
				return Tr::_('Deactivate');
			}
			
			public function perform( Entity_WithEShopData $entity, mixed $action_context ): bool
			{
				$entity->deactivateEShopData( $this->eshop );
				return true;
			}
		};
		
		
		return [
			$activate->getKey() => $activate,
			$deactivate->getKey() => $deactivate
		];
	}
	
}
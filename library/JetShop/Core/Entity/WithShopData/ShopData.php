<?php
namespace JetShop;

use Jet\Data_Text;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\DataModel_Related_1toN;
use Jet\Form;
use Jet\Locale;
use JetApplication\Entity_WithShopData_ShopData;
use JetApplication\Shop_Managers;
use JetApplication\Shops;
use JetApplication\Shops_Shop;

#[DataModel_Definition(
	database_table_name: '',
	id_controller_class: DataModel_IDController_Passive::class,
)]
abstract class Core_Entity_WithShopData_ShopData extends DataModel_Related_1toN
{
	protected ?Shops_Shop $_shop = null;
	
	
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
	protected string $shop_code = '';
	
	
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
	protected bool $is_active_for_shop = false;
	
	protected static array $loaded_items = [];
	
	
	public static function get( int $id, ?Shops_Shop $shop=null ) : ?static
	{
		$shop = $shop?:Shops::getCurrent();
		
		$key = get_called_class().':'.$shop->getKey().':'.$id;
		
		if(!array_key_exists($key, static::$loaded_items)) {
			$where = $shop->getWhere();
			$where[] = 'AND';
			$where['entity_id'] = $id;
			
			static::$loaded_items[ $key ] = static::load( $where );
		}
		
		
		return static::$loaded_items[ $key ];
	}
	
	
	public function setShop( Shops_Shop $shop ) : void
	{
		$this->shop_code = $shop->getShopCode();
		$this->locale = $shop->getLocale();
		$this->_shop = $shop;
	}
	
	public function getShopCode() : string
	{
		return $this->shop_code;
	}
	
	public function getShop() : Shops_Shop
	{
		if(!$this->_shop) {
			$this->_shop = Shops::get( $this->getShopKey() );
		}
		
		return $this->_shop;
	}
	
	public function getShopKey() : string
	{
		return $this->shop_code.'_'.$this->locale;
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
		return $this->shop_code.'_'.$this->locale;
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
		return $this->is_active_for_shop && $this->entity_is_active;
	}
	
	public function isActiveForShop(): bool
	{
		return $this->is_active_for_shop;
	}
	
	public function activate() : void
	{
		$this->is_active_for_shop = true;
		$where = $this->getShop()->getWhere();
		$where[] = 'AND';
		$where['entity_id'] = $this->entity_id;
		
		static::updateData(
			data: [
				'is_active_for_shop' => $this->is_active_for_shop
			],
			where: $where
		);
	}
	
	public function deactivate() : void
	{
		$this->is_active_for_shop = false;
		$where = $this->getShop()->getWhere();
		$where[] = 'AND';
		$where['entity_id'] = $this->entity_id;
		
		static::updateData(
			data: [
				'is_active_for_shop' => $this->is_active_for_shop
			],
			where: $where
		);
	}
	
	
	
	public function createForm( string $form_name, array $only_fields=[], array $exclude_fields=[]   ) : Form
	{
		if(!Shops::exists( $this->getShopKey() )) {
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
			$where = $this->getShop()->getWhere();
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
	
	
	/**
	 * @param array $ids
	 * @param Shops_Shop|null $shop
	 * @param array|string|null $order_by
	 * @return static[]
	 */
	public static function getActiveList( array $ids, ?Shops_Shop $shop=null, array|string|null $order_by = null ) : array
	{
		if(!$ids) {
			return [];
		}
		
		$model_name = static::getDataModelDefinition()->getModelName();
		
		$where = static::getActiveQueryWhere( $shop );
		$where[] = 'AND';
		$where['entity_id'] = $ids;
		
		return static::fetch(
			where_per_model: [ $model_name=>$where],
			order_by: $order_by,
			item_key_generator: function( Entity_WithShopData_ShopData $item ) : int {
				return $item->getEntityId();
			}
		);
		
		/*
		$items = [];
		foreach($_items as $item) {
			$items[$item->getEntityId()] = $item;
		}
		
		return $items;
		*/
	}
	
	public static function getActiveQueryWhere( ?Shops_Shop $shop=null ) : array
	{
		$shop = $shop?:Shops::getCurrent();
		
		$where = [];
		$where[] = $shop->getWhere();
		$where[] = 'AND';
		$where[] = [
			'entity_is_active' => true,
			'AND',
			'is_active_for_shop' => true
		];
		
		return $where;
	}
	
	
	public static function getNonActiveQueryWhere( ?Shops_Shop $shop=null ) : array
	{
		$shop = $shop?:Shops::getCurrent();
		
		$where = [];
		$where[] = $shop->getWhere();
		$where[] = 'AND';
		$where[] = [
			'entity_is_active' => false,
			'OR',
			'is_active_for_shop' => false
		];
		
		return $where;
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
		return Shop_Managers::Image()->getThumbnailUrl(
			$this->getImage( $image_class ),
			$max_w,
			$max_h
		);
	}
	
	public function getImageUrl( string $image_class ): string
	{
		return Shop_Managers::Image()->getUrl(
			$this->getImage( $image_class )
		);
	}
	
	
}
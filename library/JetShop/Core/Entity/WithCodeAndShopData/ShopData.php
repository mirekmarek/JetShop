<?php
namespace JetShop;

use Jet\Data_Text;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\DataModel_Related_1toN;
use Jet\Form;
use Jet\Locale;
use JetApplication\Shops;
use JetApplication\Shops_Shop;

#[DataModel_Definition(
	database_table_name: '',
	id_controller_class: DataModel_IDController_Passive::class,
)]
abstract class Core_Entity_WithCodeAndShopData_ShopData extends DataModel_Related_1toN
{
	protected ?Shops_Shop $_shop = null;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_ID,
		is_id: true,
		related_to: 'main.code',
	)]
	protected string $entity_code = '';
	
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
		type: DataModel::TYPE_BOOL,
		is_key: true,
	)]
	protected bool $is_active_for_shop = false;
	
	
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
	
	public function getEntityCode(): string
	{
		return $this->entity_code;
	}
	
	public function setEntityCode( string $entity_code ): void
	{
		$this->entity_code = $entity_code;
	}
	
	
	public function getLocale(): Locale
	{
		return $this->locale;
	}
	
	
	
	public function getArrayKeyValue() : string
	{
		return $this->shop_code.'_'.$this->locale;
	}
	
	public function isEntityActive(): bool
	{
		return $this->entity_is_active;
	}
	
	public function _setEntityIsActive( bool $entity_is_active ): void
	{
		$this->entity_is_active = $entity_is_active;
		$where = $this->getShop()->getWhere();
		$where[] = 'AND';
		$where['entity_code'] = $this->entity_code;
		
		static::updateData(
			data: [
				'entity_is_active' => $this->entity_is_active
			],
			where: $where
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
		$where['entity_code'] = $this->entity_code;
		
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
		$where['entity_code'] = $this->entity_code;
		
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
		
		/** @noinspection PhpMultipleClassDeclarationsInspection */
		return parent::createForm( $form_name, $only_fields, $exclude_fields );
	}
	
	public function _generateURLPathPart( string $name, string $type='' ) : string
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
		
		if($type) {
			return $name.'-'.$type.'-'.$this->getEntityCode();
		} else {
			return $name;
		}
		
		
	}
	
	
	public static function getActiveQueryWhere( Shops_Shop $shop ) : array
	{
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
}
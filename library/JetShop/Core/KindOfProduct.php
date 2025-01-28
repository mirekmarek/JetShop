<?php
/**
 *
 */

namespace JetShop;

use Jet\Application_Module;
use Jet\Application_Modules;
use Jet\DataModel;
use Jet\DataModel_Definition;

use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_KindOfProduct;
use JetApplication\Category;
use JetApplication\Entity_Admin_WithEShopData_Interface;
use JetApplication\Entity_Admin_WithEShopData_Trait;
use JetApplication\Entity_HasImages_Interface;
use JetApplication\Entity_WithEShopData;
use JetApplication\Entity_WithEShopData_HasImages_Trait;
use JetApplication\FulltextSearch_IndexDataProvider;
use JetApplication\Entity_Definition;
use JetApplication\KindOfProduct_PropertyGroup;
use JetApplication\KindOfProduct_EShopData;
use JetApplication\KindOfProduct;
use JetApplication\MeasureUnits;
use JetApplication\Product;
use JetApplication\Product_VirtualProductHandler;
use JetApplication\EShops;
use JetApplication\EShop;
use JetApplication\PropertyGroup;
use JetApplication\Property;
use JetApplication\KindOfProduct_Property;
use JetApplication\MeasureUnit;

/**
 *
 */
#[DataModel_Definition(
	name: 'kind_of_product',
	database_table_name: 'kind_of_product',
)]
#[Entity_Definition(
	admin_manager_interface: Admin_Managers_KindOfProduct::class,
	description_mode: true,
	images: [
		'main' => 'Main image',
		'pictogram' => 'Pictogram image',
	]
)]
abstract class Core_KindOfProduct extends Entity_WithEShopData implements
	Entity_HasImages_Interface,
	FulltextSearch_IndexDataProvider,
	Entity_Admin_WithEShopData_Interface
{
	use Entity_WithEShopData_HasImages_Trait;
	use Entity_Admin_WithEShopData_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 64
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Unit of measure:',
		is_required: false,
		select_options_creator: [
			MeasureUnits::class,
			'getScope'
		],
		error_messages: [
		]
	)]
	protected string $measure_unit = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Is virtual product',
		is_required: false,
		error_messages: [
		]
	)]
	protected bool $is_virtual_product = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 125
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Virtual product handler:',
		is_required: false,
		select_options_creator: [
			Product::class,
			'getVirtualProductHandlersOptionsScope'
		],
		error_messages: [
		]
	)]
	protected string $virtual_product_handler = '';
	
	
	
	/**
	 * @var KindOfProduct_PropertyGroup[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: KindOfProduct_PropertyGroup::class
	)]
	protected array $property_groups = [];
	
	/**
	 * @var KindOfProduct_Property[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: KindOfProduct_Property::class
	)]
	protected array $properties = [];
	
	/**
	 * @var KindOfProduct_EShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: KindOfProduct_EShopData::class
	)]
	protected array $eshop_data = [];
	
	/**
	 * @return KindOfProduct_PropertyGroup[]
	 */
	public function getPropertyGroups() : iterable
	{
		return $this->property_groups;
	}
	
	public function getPropertyGroup( int $id ) : ?KindOfProduct_PropertyGroup
	{
		if(!isset( $this->property_groups[$id])) {
			return null;
		}
		
		return $this->property_groups[$id];
	}

	
	public function getEshopData( ?EShop $eshop=null ) : KindOfProduct_EShopData
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->_getEshopData( $eshop );
	}
	
	
	
	
	public function addPropertyGroup( int $group_id ) : bool
	{
		if(isset( $this->property_groups[$group_id] )) {
			return false;
		}
		
		if(!($group=PropertyGroup::load($group_id))) {
			return false;
		}
		
		$group_assoc = new KindOfProduct_PropertyGroup();
		$group_assoc->setKindOfProductId( $this->id );
		$group_assoc->setGroupId($group_id);
		$group_assoc->setPriority( count($this->properties)+1 );
		
		$group_assoc->save();
		
		$this->property_groups[$group_id] = $group_assoc;
		
		return true;
	}
	
	public function removePropertyGroup( int $group_id ) : bool
	{
		if(
			!isset( $this->property_groups[$group_id] )
		) {
			return false;
		}
		
		$this->property_groups[$group_id]->delete();
		unset( $this->property_groups[$group_id]);
		
		$priority = 0;
		foreach( $this->property_groups as $group) {
			$priority++;
			
			$group->setPriority( $priority );
			$group->save();
		}
		
		foreach($this->properties as $property) {
			if($property->getGroupId()==$group_id) {
				$property->setGroupId( 0 );
				$property->save();
			}
		}
		
		return true;
	}
	

	

	public function getPropertyIds() : array
	{
		return array_keys($this->properties);
	}
	
	public function getVariantSelectorPropertyIds() : array
	{
		$properties = [];
		
		foreach($this->properties as $p) {
			if($p->getIsVariantSelector()) {
				$id = $p->getPropertyId();
				$properties[$id] = $id;
			}
		}
		
		return $properties;
	}
	
	
	public function getVariantSelectorProperties() : array
	{
		$ids = $this->getVariantSelectorPropertyIds();
		if(!$ids) {
			return [];
		}
		
		return Property::getProperties( $ids );
	}
	
	
	public function setPropertyIsVariantMaster( int $property_id, bool $state ) : bool
	{
		if(!$this->properties[$property_id]) {
			return false;
		}
		
		$property = $this->properties[$property_id];
		
		$property_definition = Property::load( $property->getPropertyId() );
		if(
			!$property_definition ||
			!$property_definition->canBeVariantSelector()
		) {
			return false;
		}
		
		
		$property->setIsVariantSelector( $state );
		$property->save();
		
		return true;
	}
	
	public function setShowOnProductDetail( int $property_id, bool $state ) : bool
	{
		if(!$this->properties[$property_id]) {
			return false;
		}
		
		$property = $this->properties[$property_id];
		
		$property->setShowOnProductDetail( $state );
		$property->save();
		
		return true;
	}
	
	
	/**
	 * @return KindOfProduct[]
	 */
	public static function getByProperty( Property $property ) : array
	{
		$res = [];
		
		$ids = KindOfProduct_Property::dataFetchCol(
			select: ['kind_of_product_id'],
			where: ['property_id'=>$property->getId()]
		);
		
		
		if(!$ids) {
			return [];
		}
		
		return static::fetch(['kind_of_product'=>['id'=>$ids]]);
	}
	
	
	/**
	 * @return KindOfProduct[]
	 */
	public static function getByPropertyGroup( PropertyGroup $property_group ) : array
	{
		$res = [];
		
		$ids = KindOfProduct_PropertyGroup::dataFetchCol(
			select: ['kind_of_product_id'],
			where: ['group_id'=>$property_group->getId()]
		);
		
		
		if(!$ids) {
			return [];
		}
		
		return static::fetch(['kind_of_product'=>['id'=>$ids]]);
	}
	
	
	public function addProperty( int $property_id, int $property_group_id ) : bool
	{
		if(
			isset( $this->properties[$property_id] ) ||
			!($property=Property::load($property_id))
		) {
			return false;
		}
		
		
		$property_assoc = new KindOfProduct_Property();
		$property_assoc->setKindOfProductId( $this->id );
		$property_assoc->setPropertyId( $property_id );
		$property_assoc->setPropertyType( $property->getType() );
		$property_assoc->setCanBeVariantSelector( $property->canBeVariantSelector() );
		$property_assoc->setCanBeFilter( $property->canBeFilter() );
		$property_assoc->setGroupId( $property_group_id );
		$property_assoc->setPriority( count($this->properties)+1 );
		$property_assoc->setShowOnProductDetail( true );
		
		$property_assoc->save();
		
		$this->properties[$property_id] = $property_assoc;
		
		return true;
	}
	
	public function removeProperty( int $property_id ) : bool
	{
		if(!isset( $this->properties[$property_id] )) {
			return false;
		}
		
		$this->properties[$property_id]->delete();
		unset($this->properties[$property_id]);
		
		$priority = 0;
		foreach($this->properties as $property) {
			$priority++;
			
			$property->setPriority( $priority );
			$property->save();
		}
		
		return true;
	}
	
	/**
	 * @return KindOfProduct_Property[]
	 */
	public function getProperties() : array
	{
		return $this->properties;
	}
	
	public function getProperty( int $property_id ) : ?KindOfProduct_Property
	{
		return $this->properties[$property_id]??null;
	}
	
	
	public function sortLayout( string|array $layout ) : void
	{
		if(!is_array($layout)) {
			$_layout = explode(';', $layout);
			$layout = [];
			
			foreach($_layout as $item) {
				$item = explode(':', $item);
				
				switch($item[0]??'') {
					case 'p':
						$group_id = 0;
						$property_id = (int)$item[1];
						break;
					case 'g':
						$group_id = (int)$item[1];
						$property_id = (int)$item[2];
						break;
					default: return;
				}
				
				if($group_id) {
					if(!isset($layout[$group_id])) {
						$layout[$group_id] = [];
					}
					
					$layout[$group_id][] = $property_id;
				} else {
					$layout[] = $property_id;
				}
			}
		}
		

		foreach($layout as $k=>$v) {
			if(is_array($v)) {
				$group_id = $k;
				if(!isset($this->property_groups[$group_id])) {
					return;
				}
				
				foreach($v as $property_id) {
					if(!isset($this->properties[$property_id])) {
						return;
					}
				}
			} else {
				$property_id = $v;
				
				if(!isset($this->properties[$property_id])) {
					return;
				}
			}
		}
		
		$priority = 0;
		foreach($layout as $k=>$v) {
			if(is_array($v)) {
				$group_id = $k;
				
				$priority++;
				$this->property_groups[$group_id]->setPriority( $priority );
				$this->property_groups[$group_id]->save();
				
				foreach($v as $property_id) {
					$priority++;
					$this->properties[$property_id]->setPriority( $priority );
					$this->properties[$property_id]->setGroupId( $group_id );
					$this->properties[$property_id]->save();
				}
			} else {
				$property_id = $v;
				
				$priority++;
				$this->properties[$property_id]->setPriority( $priority );
				$this->properties[$property_id]->setGroupId( 0 );
				$this->properties[$property_id]->save();
			}
		}
		
		uasort( $this->property_groups, function( KindOfProduct_PropertyGroup $a, KindOfProduct_PropertyGroup $b ) {
			return $a->getPriority() <=>  $b->getPriority();
		} );
		
		uasort( $this->properties, function( KindOfProduct_Property $a, KindOfProduct_Property $b ) {
			return $a->getPriority() <=> $b->getPriority();
		} );
		
	}
	
	public function getLayout( bool $add_empty_groups = false ) : array
	{
		$layout = [];
		$current_group = 0;
		$added_groups = [];
		
		foreach($this->properties as $property) {
			$group_id = $property->getGroupId();
			$property_id = $property->getPropertyId();
			
			if(
				$group_id &&
				!isset($this->property_groups[$group_id])
			) {
				$group_id = 0;
			}
			
			if(!$group_id) {
				$layout[] = $property_id;
				continue;
			}
			
			if(!isset($layout[$group_id])) {
				$added_groups[] = $group_id;
				$layout[$group_id] = [];
			}
			
			$layout[$group_id][] = $property_id;
		}
		
		if($add_empty_groups) {
			foreach($this->property_groups as $group_id=>$group) {
				if(!in_array($group_id, $added_groups)) {
					$layout[$group_id] = [];
				}
			}
		}
		
		return $layout;
	}
	
	
	public function getFulltextObjectType(): string
	{
		return '';
	}
	
	public function getFulltextObjectIsActive(): bool
	{
		return $this->isActive();
	}
	
	public function getInternalFulltextObjectTitle(): string
	{
		return $this->getAdminTitle();
	}
	
	public function getInternalFulltextTexts(): array
	{
		return [$this->getInternalName(), $this->getInternalCode()];
	}
	
	public function getShopFulltextTexts( EShop $eshop ) : array
	{
		return [];
	}
	
	public function updateFulltextSearchIndex() : void
	{
		Admin_Managers::FulltextSearch()->updateIndex( $this );
	}
	
	public function removeFulltextSearchIndex() : void
	{
		Admin_Managers::FulltextSearch()->deleteIndex( $this );
	}

	public function getUsageCategoryIds() : array
	{
		return Category::dataFetchCol(
			select: ['id'],
			where: ['kind_of_product_id'=>$this->id]
		);
	}
	
	public function setMeasureUnit( string|MeasureUnit $value ) : void
	{
		$this->measure_unit = $value;
		foreach( EShops::getList() as $eshop ) {
			$this->getEshopData( $eshop )->setMeasureUnit( $value );
		}
	}
	
	public function getMeasureUnit() : ?MeasureUnit
	{
		return MeasureUnits::get( $this->measure_unit );
	}
	
	public function setIsVirtualProduct( bool $value ) : void
	{
		$this->is_virtual_product = $value;
		foreach( EShops::getList() as $eshop ) {
			$this->getEshopData( $eshop )->setIsVirtualProduct( $value );
		}
	}
	
	
	public function getIsVirtualProduct() : bool
	{
		return $this->is_virtual_product;
	}
	
	public function getVirtualProductHandler(): null|Product_VirtualProductHandler|Application_Module
	{
		if(
			!$this->virtual_product_handler ||
			!Application_Modules::moduleIsActivated( $this->virtual_product_handler )
		) {
			return null;
		}
		
		return Application_Modules::moduleInstance( $this->virtual_product_handler );
	}
	
	public function setVirtualProductHandler( string $value ): void
	{
		$this->virtual_product_handler = $value;
		foreach( EShops::getList() as $eshop ) {
			$this->getEshopData( $eshop )->setVirtualProductHandler( $value );
		}
	}
}

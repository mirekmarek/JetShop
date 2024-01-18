<?php
/**
 *
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Entity_WithShopData;
use JetApplication\KindOfProduct_PropertyGroup;
use JetApplication\KindOfProduct_ShopData;
use JetApplication\KindOfProduct;
use JetApplication\Property_ShopData;
use JetApplication\PropertyGroup_ShopData;
use JetApplication\Shops;
use JetApplication\Shops_Shop;
use JetApplication\PropertyGroup;
use JetApplication\Property;
use JetApplication\KindOfProduct_Property;

/**
 *
 */
#[DataModel_Definition(
	name: 'kind_of_product',
	database_table_name: 'kind_of_product',
)]
abstract class Core_KindOfProduct extends Entity_WithShopData
{
	
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
	 * @var KindOfProduct_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: KindOfProduct_ShopData::class
	)]
	protected array $shop_data = [];

	

	
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

	
	public function getShopData( ?Shops_Shop $shop=null ) : KindOfProduct_ShopData
	{
		return $this->shop_data[$shop ? $shop->getKey() : Shops::getCurrent()->getKey()];
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
		
		//TODO: auto append default group properties
		
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
	
	public function setUseInFilters( int $property_id, bool $state ) : bool
	{
		if(!$this->properties[$property_id]) {
			return false;
		}
		
		$property = $this->properties[$property_id];
		
		$property_definition = Property::load( $property->getPropertyId() );
		if(
			!$property_definition ||
			!$property_definition->canBeFilter()
		) {
			return false;
		}
		
		
		$property->setUseInFilters( $state );
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
		$property_assoc->setUseInFilters( $property->canBeFilter() );
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
			if($a->getPriority()<$b->getPriority()) {
				return -1;
			}
			if($a->getPriority()>$b->getPriority()) {
				return 1;
			}
			
			return 0;
		} );
		
		uasort( $this->properties, function( KindOfProduct_Property $a, KindOfProduct_Property $b ) {
			if($a->getPriority()<$b->getPriority()) {
				return -1;
			}
			if($a->getPriority()>$b->getPriority()) {
				return 1;
			}
			
			return 0;
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
	
	/**
	 * @param PropertyGroup_ShopData[] &$groups
	 * @param Property_ShopData[] &$properties
	 * @param Shops_Shop|null $shop
	 * @return array
	 */
	public function getProductDetailLayout( array &$groups=[], array &$properties=[], ?Shops_Shop $shop=null ) : array
	{
		
		$group_ids = [];
		$property_ids = [];

		foreach($this->properties as $property) {
			if(!$property->getShowOnProductDetail()) {
				continue;
			}
			
			$group_id = $property->getGroupId();
			$property_id = $property->getPropertyId();

			if(
				$group_id &&
				!isset($this->property_groups[$group_id])
			) {
				$group_id = 0;
			}
			
			if(
				$group_id &&
				!in_array($group_id, $group_ids)
			) {
				$group_ids[] = $group_id;
			}
			$property_ids[] = $property_id;
		}

		if(!$property_ids) {
			return [];
		}
		

		$properties = Property_ShopData::getActiveList( $property_ids, $shop );
		$groups = PropertyGroup_ShopData::getActiveList( $group_ids, $shop );
		
		
		$layout = [];
		$current_group = 0;
		foreach($this->properties as $property) {
			$property_id = $property->getPropertyId();
			if(!isset($properties[$property_id])) {
				continue;
			}

			
			$group_id = $property->getGroupId();
			
			if(
				$group_id &&
				!isset($groups[$group_id])
			) {
				$group_id = 0;
			}
			
			if(!$group_id) {
				$layout[] = $property_id;
				continue;
			}
			
			if(!isset($layout[$group_id])) {
				$layout[$group_id] = [];
			}
			
			$layout[$group_id][] = $property_id;
		}
		
		return $layout;
	}
	
}

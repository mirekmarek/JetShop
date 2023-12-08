<?php
/**
 *
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Entity_WithIDAndShopData;
use JetApplication\KindOfProduct_FilterGroup;
use JetApplication\KindOfProduct_DetailGroup;
use JetApplication\KindOfProduct_HiddenProperty;
use JetApplication\KindOfProduct_ShopData;
use JetApplication\KindOfProduct;
use JetApplication\Shops;
use JetApplication\Shops_Shop;
use JetApplication\PropertyGroup;
use JetApplication\Property;
use JetApplication\KindOfProduct_FilterGroup_Property;
use JetApplication\KindOfProduct_DetailGroup_Property;

/**
 *
 */
#[DataModel_Definition(
	name: 'kind_of_product',
	database_table_name: 'kind_of_product',
)]
abstract class Core_KindOfProduct extends Entity_WithIDAndShopData
{
	
	/**
	 * @var KindOfProduct_FilterGroup[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: KindOfProduct_FilterGroup::class
	)]
	protected array $filter_groups = [];
	
	/**
	 * @var KindOfProduct_DetailGroup[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: KindOfProduct_DetailGroup::class
	)]
	protected array $detail_groups = [];
	
	
	/**
	 * @var KindOfProduct_HiddenProperty[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: KindOfProduct_HiddenProperty::class
	)]
	protected array $hidden_properties = [];
	
	/**
	 * @var KindOfProduct_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: KindOfProduct_ShopData::class
	)]
	protected array $shop_data = [];

	
	/**
	 * @return KindOfProduct_FilterGroup[]
	 */
	public function getFilterGroups() : iterable
	{
		return $this->filter_groups;
	}
	
	public function getFilterGroup( int $id ) : ?KindOfProduct_FilterGroup
	{
		if(!isset($this->filter_groups[$id])) {
			return null;
		}
		
		return $this->filter_groups[$id];
	}
	
	/**
	 * @return KindOfProduct_DetailGroup[]
	 */
	public function getDetailGroups() : iterable
	{
		return $this->detail_groups;
	}
	
	public function getDetailGroup( int $id ) : ?KindOfProduct_DetailGroup
	{
		if(!isset($this->detail_groups[$id])) {
			return null;
		}
		
		return $this->detail_groups[$id];
	}

	
	public function getShopData( ?Shops_Shop $shop=null ) : KindOfProduct_ShopData
	{
		return $this->shop_data[$shop ? $shop->getKey() : Shops::getCurrent()->getKey()];
	}
	
	
	
	
	public function addDetailPropertyGroup( int $group_id ) : bool
	{
		if(isset( $this->detail_groups[$group_id] )) {
			return false;
		}
		
		if(!PropertyGroup::exists($group_id)) {
			return false;
		}
		
		$detail_group = new KindOfProduct_DetailGroup();
		$detail_group->setKindOfProductId( $this->id );
		$detail_group->setGroupId($group_id);
		$detail_group->setPriority( count($this->detail_groups)+1 );

		$detail_group->save();
		
		$this->detail_groups[$group_id] = $detail_group;
		
		return true;
	}
	
	public function removeDetailPropertyGroup( int $group_id ) : bool
	{
		if(!isset( $this->detail_groups[$group_id] )) {
			return false;
		}
		
		$this->detail_groups[$group_id]->delete();
		unset( $this->detail_groups[$group_id]);
		
		$priority = 0;
		foreach($this->detail_groups as $group) {
			$priority++;
			
			$group->setPriority( $priority );
			$group->save();
		}
		
		return true;
	}
	
	public function sortDetailGroups( array $sort ) : bool
	{
		if(count($sort)!=count($this->detail_groups)) {
			return false;
		}
		foreach($sort as $id) {
			if(!isset($this->detail_groups[$id])) {
				return false;
			}
		}
		
		$priority = 0;
		foreach($sort as $id) {
			$priority++;
			$this->detail_groups[$id]->setPriority( $priority );
			$this->detail_groups[$id]->save();
		}
		
		uasort( $this->detail_groups, function( KindOfProduct_DetailGroup $a, KindOfProduct_DetailGroup $b ) {
			if($a->getPriority()<$b->getPriority()) {
				return -1;
			}
			if($a->getPriority()>$b->getPriority()) {
				return 1;
			}
			
			return 0;
		} );
		
		return true;
	}
	
	
	public function addFilterPropertyGroup( int $group_id ) : bool
	{
		if(isset( $this->filter_groups[$group_id] )) {
			return false;
		}
		
		if(!PropertyGroup::exists($group_id)) {
			return false;
		}
		
		$filter_group = new KindOfProduct_FilterGroup();
		$filter_group->setKindOfProductId( $this->id );
		$filter_group->setGroupId($group_id);
		$filter_group->setPriority( count($this->filter_groups)+1 );
		
		$filter_group->save();
		
		$this->filter_groups[$group_id] = $filter_group;
		
		return true;
	}
	
	public function removeFilterPropertyGroup( int $group_id ) : bool
	{
		if(!isset( $this->filter_groups[$group_id] )) {
			return false;
		}
		
		$this->filter_groups[$group_id]->delete();
		unset( $this->filter_groups[$group_id]);
		
		$priority = 0;
		foreach($this->filter_groups as $group) {
			$priority++;
			
			$group->setPriority( $priority );
			$group->save();
		}
		
		return true;
	}
	
	public function sortFilterGroups( array $sort ) : bool
	{
		if(count($sort)!=count($this->filter_groups)) {
			return false;
		}
		foreach($sort as $id) {
			if(!isset($this->filter_groups[$id])) {
				return false;
			}
		}
		
		$priority = 0;
		foreach($sort as $id) {
			$priority++;
			$this->filter_groups[$id]->setPriority( $priority );
			$this->filter_groups[$id]->save();
		}
		
		uasort( $this->filter_groups, function( KindOfProduct_FilterGroup $a, KindOfProduct_FilterGroup $b ) {
			if($a->getPriority()<$b->getPriority()) {
				return -1;
			}
			if($a->getPriority()>$b->getPriority()) {
				return 1;
			}
			
			return 0;
		} );
		
		return true;
	}
	
	public function getAllPropertyIds() : array
	{
		$properties = [];
		
		foreach($this->getDetailGroups() as $group) {
			foreach($group->getProperties() as $p) {
				$id = $p->getPropertyId();
				$properties[$id] = $id;
			}
		}
		
		foreach($this->getFilterGroups() as $group) {
			foreach($group->getProperties() as $p) {
				$id = $p->getPropertyId();
				$properties[$id] = $id;
			}
		}
		
		foreach($this->hidden_properties as $p) {
			$id = $p->getPropertyId();
			$properties[$id] = $id;
		}
		
		return $properties;
	}
	
	public function getVariantSelectorPropertyIds() : array
	{
		$properties = [];
		
		foreach($this->getDetailGroups() as $group) {
			foreach($group->getProperties() as $p) {
				if($p->getIsVariantSelector()) {
					$id = $p->getPropertyId();
					$properties[$id] = $id;
				}
			}
		}
		
		return $properties;
	}
	
	public function setDetailByFilter() : bool
	{
		foreach($this->detail_groups as $g) {
			$g->delete();
		}
		$this->detail_groups = [];
		
		foreach($this->filter_groups as $f_g) {
			$this->addDetailPropertyGroup($f_g->getGroupId());
			
			$d_g = $this->detail_groups[$f_g->getGroupId()];
			
			foreach($f_g->getProperties() as $f_p) {
				$d_g->addProperty( $f_p->getPropertyId() );
			}
		}
		
		//TODO: sync
		
		return true;
	}
	
	public function setFilterByDetail() : bool
	{
		foreach($this->filter_groups as $g) {
			$g->delete();
		}
		$this->filter_groups = [];
		
		foreach($this->detail_groups as $d_g) {
			$this->addFilterPropertyGroup($d_g->getGroupId());
			
			$f_g = $this->filter_groups[$d_g->getGroupId()];
			
			foreach($d_g->getProperties() as $d_p) {
				$f_g->addProperty( $d_p->getPropertyId() );
			}
		}
		
		//TODO: sync
		
		return true;
	}
	
	public function setPropertyIsVariantMaster( int $property_id, bool $state ) : bool
	{
		$property = null;
		foreach($this->detail_groups as $group) {
			$property = $group->getProperty( $property_id );
			if($property) {
				break;
			}
		}
		
		if(!$property) {
			return false;
		}
		
		$property->setIsVariantSelector( $state );
		$property->save();
		
		return true;
	}
	
	
	/**
	 * @return KindOfProduct[]
	 */
	public static function getByProperty( Property $property ) : array
	{
		$res = [];
		
		$detail_ids = KindOfProduct_DetailGroup_Property::dataFetchCol(
			select: ['kind_of_product_id'],
			where: ['property_id'=>$property->getId()]
		);
		$filter_ids = KindOfProduct_FilterGroup_Property::dataFetchCol(
			select: ['kind_of_product_id'],
			where: ['property_id'=>$property->getId()]
		);
		$hidden_ids = KindOfProduct_HiddenProperty::dataFetchCol(
			select: ['kind_of_product_id'],
			where: ['property_id'=>$property->getId()]
		);
		
		$ids = array_unique(array_merge($detail_ids, $filter_ids, $hidden_ids));
		
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
		
		$detail_ids = KindOfProduct_DetailGroup::dataFetchCol(
			select: ['kind_of_product_id'],
			where: ['group_id'=>$property_group->getId()]
		);
		$filter_ids = KindOfProduct_FilterGroup::dataFetchCol(
			select: ['kind_of_product_id'],
			where: ['group_id'=>$property_group->getId()]
		);
		
		$ids = array_unique(array_merge($detail_ids, $filter_ids));
		
		if(!$ids) {
			return [];
		}
		
		return static::fetch(['kind_of_product'=>['id'=>$ids]]);
	}
	
	/**
	 * @return KindOfProduct_HiddenProperty[]
	 */
	public function getHiddenProperties(): array
	{
		return $this->hidden_properties;
	}

	public function addHiddenProperty( int $property_id ) : bool
	{
		if(isset( $this->hidden_properties[$property_id] )) {
			return false;
		}
		
		if(!Property::exists($property_id)) {
			return false;
		}
		
		$hidden_property = new KindOfProduct_HiddenProperty();
		$hidden_property->setKindOfProductId( $this->id );
		$hidden_property->setPropertyId($property_id);
		$hidden_property->setPriority( count($this->hidden_properties)+1 );
		
		$hidden_property->save();
		
		$this->hidden_properties[$property_id] = $hidden_property;
		
		return true;
		
	}
	
	public function removeHiddenProperty( int $property_id ) : bool
	{
		if(!isset( $this->hidden_properties[$property_id] )) {
			return false;
		}
		
		$this->hidden_properties[$property_id]->delete();
		unset( $this->hidden_properties[$property_id]);
		
		$priority = 0;
		foreach($this->hidden_properties as $property) {
			$priority++;
			
			$property->setPriority( $priority );
			$property->save();
		}
		
		return true;
	}
	
	public function sortHiddenProperties( array $sort ) : bool
	{
		if(count($sort)!=count($this->hidden_properties)) {
			return false;
		}
		foreach($sort as $id) {
			if(!isset($this->hidden_properties[$id])) {
				return false;
			}
		}
		
		$priority = 0;
		foreach($sort as $id) {
			$priority++;
			$this->hidden_properties[$id]->setPriority( $priority );
			$this->hidden_properties[$id]->save();
		}
		
		uasort( $this->hidden_properties, function( KindOfProduct_HiddenProperty $a, KindOfProduct_HiddenProperty $b ) {
			if($a->getPriority()<$b->getPriority()) {
				return -1;
			}
			if($a->getPriority()>$b->getPriority()) {
				return 1;
			}
			
			return 0;
		} );
		
		return true;
	}
	
}

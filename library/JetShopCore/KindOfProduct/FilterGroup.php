<?php
/**
 *
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\DataModel_Related_1toN;

/**
 *
 */
#[DataModel_Definition(
	name: 'kind_of_product_filter_group',
	database_table_name: 'kind_of_product_filter_groups',
	parent_model_class: KindOfProduct::class,
	id_controller_class: DataModel_IDController_Passive::class,
	default_order_by: ['+priority']
)]
abstract class Core_KindOfProduct_FilterGroup extends DataModel_Related_1toN
{
	
	#[DataModel_Definition(
		related_to: 'main.id',
		is_key: true
	)]
	protected int $kind_of_product_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true
	)]
	protected int $group_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	protected int $priority = 0;
	
	/**
	 * @var KindOfProduct_FilterGroup_Property[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: KindOfProduct_FilterGroup_Property::class
	)]
	protected array $properties = [];
	
	public function getArrayKeyValue() : string
	{
		return $this->group_id;
	}
	
	public function getKindOfProductId(): int
	{
		return $this->kind_of_product_id;
	}
	
	public function setKindOfProductId( int $kind_of_product_id ): void
	{
		$this->kind_of_product_id = $kind_of_product_id;
	}
	
	public function getGroupId(): int
	{
		return $this->group_id;
	}
	
	public function setGroupId( int $group_id ): void
	{
		$this->group_id = $group_id;
	}
	
	public function getGroup() : PropertyGroup
	{
		return PropertyGroup::get($this->group_id);
	}
	
	public function getPriority(): int
	{
		return $this->priority;
	}
	
	public function setPriority( int $priority ): void
	{
		$this->priority = $priority;
	}
	
	public function addProperty( int $property_id ) : bool
	{
		if(isset( $this->properties[$property_id] )) {
			return false;
		}
		
		$property = Property::get($property_id);
		if(!$property) {
			return false;
		}
		
		$detail_group_property = new KindOfProduct_FilterGroup_Property();
		$detail_group_property->setKindOfProductId( $this->kind_of_product_id );
		$detail_group_property->setGroupId( $this->group_id );
		$detail_group_property->setPropertyId( $property_id );
		$detail_group_property->setPriority( count($this->properties)+1 );
		
		$detail_group_property->save();
		
		$this->properties[$property_id] = $detail_group_property;
		
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
	
	public function sortProperties( array $sort ) : bool
	{
		if(count($sort)!=count($this->properties)) {
			return false;
		}
		
		foreach($sort as $id) {
			if(!isset($this->properties[$id])) {
				return false;
			}
		}
		
		$priority = 0;
		foreach($sort as $id) {
			$priority++;
			$this->properties[$id]->setPriority( $priority );
			$this->properties[$id]->save();
		}
		
		uasort( $this->properties, function( KindOfProduct_FilterGroup_Property $a, KindOfProduct_FilterGroup_Property $b ) {
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
	
	/**
	 * @return KindOfProduct_FilterGroup_Property[]
	 */
	public function getProperties() : array
	{
		return $this->properties;
	}
	
}

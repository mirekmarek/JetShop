<?php
/**
 * 
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\DataModel_Related_1toN;

use JetApplication\Property;
use JetApplication\KindOfProduct_DetailGroup;

/**
 *
 */
#[DataModel_Definition(
	name: 'kind_of_product_detail_group_property',
	database_table_name: 'kind_of_product_detail_group_property',
	parent_model_class: KindOfProduct_DetailGroup::class,
	default_order_by: [
		'+priority'
	],
	id_controller_class: DataModel_IDController_Passive::class
)]
abstract class Core_KindOfProduct_DetailGroup_Property extends DataModel_Related_1toN
{
	
	#[DataModel_Definition(
		related_to: 'main.id',
		is_key: true
	)]
	protected int $kind_of_product_id = 0;
	
	#[DataModel_Definition(
		related_to: 'parent.group_id',
		is_key: true
	)]
	protected int $group_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true
	)]
	protected int $property_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $priority = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	protected bool $is_variant_selector = false;


	public function getArrayKeyValue() : string
	{
		return $this->property_id;
	}
	
	public function setPropertyId( int $value ) : void
	{
		$this->property_id = $value;
	}
	
	public function getPropertyId() : string
	{
		return $this->property_id;
	}
	
	
	public function getProperty() : Property
	{
		return Property::get($this->property_id);
	}

	public function setKindOfProductId( int $kind_of_product_id ): void
	{
		$this->kind_of_product_id = $kind_of_product_id;
	}

	public function getKindOfProductId() : int
	{
		return $this->kind_of_product_id;
	}
	

	public function setGroupId( int $group_id ): void
	{
		$this->group_id = $group_id;
	}
	
	
	public function getGroupId() : int
	{
		return $this->group_id;
	}


	public function setPriority( int $value ) : void
	{
		$this->priority = $value;
	}
	
	public function getPriority() : int
	{
		return $this->priority;
	}
	
	public function setIsVariantSelector( bool $value ) : void
	{
		$this->is_variant_selector = $value;
	}

	public function getIsVariantSelector() : bool
	{
		return $this->is_variant_selector;
	}
}

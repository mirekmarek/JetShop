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
	name: 'kind_of_product_hidden_property',
	database_table_name: 'kind_of_product_hidden_properties',
	parent_model_class: KindOfProduct::class,
	id_controller_class: DataModel_IDController_Passive::class,
	default_order_by: ['+priority']
)]
abstract class Core_KindOfProduct_HiddenProperty extends DataModel_Related_1toN
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
	protected int $property_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	protected int $priority = 0;
	

	public function getArrayKeyValue() : string
	{
		return $this->property_id;
	}
	
	public function getKindOfProductId(): int
	{
		return $this->kind_of_product_id;
	}
	
	public function setKindOfProductId( int $kind_of_product_id ): void
	{
		$this->kind_of_product_id = $kind_of_product_id;
	}
	
	public function getPropertyId(): int
	{
		return $this->property_id;
	}
	
	public function setPropertyId( int $property_id ): void
	{
		$this->property_id = $property_id;
	}
	
	public function getProperty() : Property
	{
		return Property::get($this->property_id);
	}
	
	public function getPriority(): int
	{
		return $this->priority;
	}
	
	public function setPriority( int $priority ): void
	{
		$this->priority = $priority;
	}
}

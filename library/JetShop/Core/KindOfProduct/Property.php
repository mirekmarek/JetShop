<?php
/**
 * 
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\DataModel_Related_1toN;

use JetApplication\KindOfProduct;


/**
 *
 */
#[DataModel_Definition(
	name: 'kind_of_product_property',
	database_table_name: 'kind_of_product_property',
	parent_model_class: KindOfProduct::class,
	default_order_by: [
		'+priority'
	],
	id_controller_class: DataModel_IDController_Passive::class
)]
abstract class Core_KindOfProduct_Property extends DataModel_Related_1toN
{
	
	#[DataModel_Definition(
		related_to: 'main.id',
		is_key: true
	)]
	protected int $kind_of_product_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
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
		type: DataModel::TYPE_STRING,
		max_len: 50
	)]
	protected string $property_type = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	protected bool $can_be_variant_selector = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	protected bool $can_be_filter = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $priority = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	protected bool $is_variant_selector = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	protected bool $show_on_product_detail = false;
	

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
	
	public function getPropertyType(): string
	{
		return $this->property_type;
	}
	
	public function setPropertyType( string $property_type ): void
	{
		$this->property_type = $property_type;
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
	
	public function getShowOnProductDetail(): bool
	{
		return $this->show_on_product_detail;
	}
	
	public function setShowOnProductDetail( bool $show_on_product_detail ): void
	{
		$this->show_on_product_detail = $show_on_product_detail;
	}
	

	public function canBeVariantSelector(): bool
	{
		return $this->can_be_variant_selector;
	}
	
	public function setCanBeVariantSelector( bool $can_be_variant_selector ): void
	{
		$this->can_be_variant_selector = $can_be_variant_selector;
	}
	
	public function canBeFilter(): bool
	{
		return $this->can_be_filter;
	}
	
	public function setCanBeFilter( bool $can_be_filter ): void
	{
		$this->can_be_filter = $can_be_filter;
	}
	
	
	
}

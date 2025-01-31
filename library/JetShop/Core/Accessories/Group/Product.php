<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\DataModel_Related_1toN;

use JetApplication\Accessories_Group;

#[DataModel_Definition(
	name: 'accessories_group_product',
	database_table_name: 'accessories_groups_products',
	parent_model_class: Accessories_Group::class,
	default_order_by: [
		'+priority'
	],
	id_controller_class: DataModel_IDController_Passive::class
)]
abstract class Core_Accessories_Group_Product extends DataModel_Related_1toN
{
	
	#[DataModel_Definition(
		related_to: 'main.id',
		is_key: true
	)]
	protected int $accessories_group_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true
	)]
	protected int $product_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $priority = 0;
	
	
	public function getArrayKeyValue() : string
	{
		return $this->product_id;
	}
	
	public function setProductId( int $value ) : void
	{
		$this->product_id = $value;
	}
	
	public function getProductId() : string
	{
		return $this->product_id;
	}
	
	public function setAccessoriesGroupId( int $accessories_group_id ): void
	{
		$this->accessories_group_id = $accessories_group_id;
	}
	
	public function getAccessoriesGroupId() : int
	{
		return $this->accessories_group_id;
	}
	
	
	public function setPriority( int $value ) : void
	{
		$this->priority = $value;
	}
	
	public function getPriority() : int
	{
		return $this->priority;
	}
	
}

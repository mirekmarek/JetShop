<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\DataModel_Related_1toN;

use JetApplication\Category;

#[DataModel_Definition(
	name: 'category_product',
	database_table_name: 'categories_products',
	id_controller_class: DataModel_IDController_Passive::class,
	default_order_by: ['priority'],
	parent_model_class: Category::class
)]
abstract class Core_Category_Product extends DataModel_Related_1toN
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		related_to: 'main.id',
		is_id: true,
		is_key: true
	)]
	protected int $category_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true,
	)]
	protected int $product_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $priority = 0;
	
	
	protected Category|null $category = null;
	
	public function getArrayKeyValue() : string
	{
		return $this->product_id;
	}
	
	public function getProductId() : int
	{
		return $this->product_id;
	}
	
	public function setProductId( int $product_id ) : void
	{
		$this->product_id = $product_id;
	}
	
	public function getCategoryId() : int
	{
		return $this->category_id;
	}
	
	public function setCategoryId( int $category_id ) : void
	{
		$this->category_id = $category_id;
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
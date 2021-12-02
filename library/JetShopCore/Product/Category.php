<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\DataModel_Related_1toN;


#[DataModel_Definition(
	name: 'products_categories',
	database_table_name: 'products_categories',
	id_controller_class: DataModel_IDController_Passive::class,
	parent_model_class: Product::class
)]
abstract class Core_Product_Category extends DataModel_Related_1toN
{

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true,
		related_to: 'main.id',
		form_field_type: false
	)]
	protected int $product_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true
	)]
	protected int $category_id = 0;

	protected Category|null $category = null;

	public function getArrayKeyValue() : string
	{
		return $this->category_id;
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

}
<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;

use JetApplication\Product;

#[DataModel_Definition(
	name: 'products_similar',
	database_table_name: 'products_similar',
	id_controller_class: DataModel_IDController_Passive::class,
	parent_model_class: Product::class
)]
abstract class Core_Product_Similar extends DataModel
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true
	)]
	protected int $product_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true
	)]
	protected int $similar_product_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $sort_order = 0;
	
	
	public function getProductId() : int
	{
		return $this->product_id;
	}
	
	public function setProductId( int $product_id ) : void
	{
		$this->product_id = $product_id;
	}
	
	public function getSimilarProductId() : string
	{
		return $this->similar_product_id;
	}
	
	public function setSimilarProductId( string $similar_product_id ) : void
	{
		$this->similar_product_id = $similar_product_id;
	}
	
	public function getSortOrder(): int
	{
		return $this->sort_order;
	}
	
	public function setSortOrder( int $sort_order ): void
	{
		$this->sort_order = $sort_order;
	}
	
}
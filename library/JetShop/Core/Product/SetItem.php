<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\DataModel_Related_1toN;

use JetApplication\Product;

/**
 *
 */
#[DataModel_Definition(
	name: 'products_set_items',
	database_table_name: 'products_set_items',
	parent_model_class: Product::class,
	default_order_by: [
		'+sort_order'
	],
	id_controller_class: DataModel_IDController_Passive::class
)]
abstract class Core_Product_SetItem extends DataModel_Related_1toN
{

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true,
		related_to: 'main.id',
	)]
	protected int $product_id = 0;

	protected Product|null $product = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true
	)]
	protected int $item_product_id = 0;

	protected Product|null $item_product = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $count = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $sort_order = 0;

	public function getArrayKeyValue() : string
	{
		return $this->item_product_id;
	}

	public function getProductId() : int
	{
		return $this->product_id;
	}

	public function setProductId( int $product_id ) : void
	{
		$this->product_id = $product_id;
	}

	public function getItemProductId() : int
	{
		return $this->item_product_id;
	}

	public function setItemProductId( int $item_product_id ) : void
	{
		$this->item_product_id = $item_product_id;
	}

	public function getSortOrder() : int
	{
		return $this->sort_order;
	}

	public function setSortOrder( int $sort_order ) : void
	{
		$this->sort_order = $sort_order;
	}

	public function getCount(): int
	{
		return $this->count;
	}

	public function setCount( int $count ): void
	{
		$this->count = $count;
	}


}
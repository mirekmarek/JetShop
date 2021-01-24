<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\DataModel_Related_1toN;


#[DataModel_Definition(
	name: 'products_stickers',
	database_table_name: 'products_stickers',
	id_controller_class: DataModel_IDController_Passive::class,
	parent_model_class: Product::class
)]
abstract class Core_Product_Sticker extends DataModel_Related_1toN
{

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true,
		related_to: 'main.id',
		form_field_type: false
	)]
	protected int $product_id = 0;

	protected Product|null $product = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true
	)]
	protected int $sticker_id = 0;

	protected Sticker|null $sticker = null;

	public function getArrayKeyValue() : int|string|null
	{
		return $this->sticker_id;
	}

	public function setParents( Product $product ) : void
	{
		$this->product = $product;
		$this->product_id = $product->getId();
	}

	public function getProductId() : int
	{
		return $this->product_id;
	}

	public function setProductId( int $product_id ) : void
	{
		$this->product_id = $product_id;
	}

	public function getStickerId() : int
	{
		return $this->sticker_id;
	}

	public function setStickerId( int $sticker_id ) : void
	{
		$this->sticker_id = $sticker_id;
	}
}
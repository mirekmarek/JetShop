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

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true
	)]
	protected string $sticker_code = '';

	protected Sticker|null $sticker = null;

	public function getArrayKeyValue() : string
	{
		return $this->sticker_code;
	}

	public function getProductId() : int
	{
		return $this->product_id;
	}

	public function setProductId( int $product_id ) : void
	{
		$this->product_id = $product_id;
	}

	public function getStickerCode() : string
	{
		return $this->sticker_code;
	}

	public function setStickerCode( string $sticker_code ) : void
	{
		$this->sticker_code = $sticker_code;
	}
}
<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

#[DataModel_Definition(
	name: 'index_internal_product',
	database_table_name: 'fulltext_internal_products_words'
)]
abstract class Core_Fulltext_Index_Internal_Product_Word extends Fulltext_Index_Word {

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true
	)]
	protected string $product_type = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $product_is_active = false;

	public function getProductType() : string
	{
		return $this->product_type;
	}

	public function setProductType( string $product_type ) : void
	{
		$this->product_type = $product_type;
	}

	public function getProductIsActive() : bool
	{
		return $this->product_is_active;
	}

	public function setProductIsActive( string $product_is_active ) : void
	{
		$this->product_is_active = $product_is_active;
	}


}

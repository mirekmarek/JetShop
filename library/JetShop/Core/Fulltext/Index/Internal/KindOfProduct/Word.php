<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Fulltext_Index_Word;

#[DataModel_Definition(
	name: 'index_internal_kind_of_product_word',
	database_table_name: 'fulltext_internal_kinds_of_products_words'
)]
abstract class Core_Fulltext_Index_Internal_KindOfProduct_Word extends Fulltext_Index_Word {

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $kind_is_active = false;


	public function getKindIsActive() : bool
	{
		return $this->kind_is_active;
	}

	public function setKindIsActive( string $kind_is_active ) : void
	{
		$this->kind_is_active = $kind_is_active;
	}


}

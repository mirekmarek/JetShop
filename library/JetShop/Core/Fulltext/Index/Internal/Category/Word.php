<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Fulltext_Index_Word;

#[DataModel_Definition(
    name: 'index_internal_category',
	database_table_name: 'fulltext_internal_categories_words'
)]
abstract class Core_Fulltext_Index_Internal_Category_Word extends Fulltext_Index_Word {

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $category_is_active = false;

	public function getCategoryIsActive() : string
	{
		return $this->category_is_active;
	}

	public function setCategoryIsActive( string $category_is_active ) : void
	{
		$this->category_is_active = $category_is_active;
	}


}

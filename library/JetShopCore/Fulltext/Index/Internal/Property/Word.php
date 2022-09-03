<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

#[DataModel_Definition(
	name: 'index_internal_property_word',
	database_table_name: 'fulltext_internal_properties_words'
)]
abstract class Core_Fulltext_Index_Internal_Property_Word extends Fulltext_Index_Word {

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true
	)]
	protected string $property_type = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $property_is_active = false;

	public function getPropertyType() : string
	{
		return $this->property_type;
	}

	public function setPropertyType( string $property_type ) : void
	{
		$this->property_type = $property_type;
	}

	public function getPropertyIsActive() : bool
	{
		return $this->property_is_active;
	}

	public function setPropertyIsActive( string $property_is_active ) : void
	{
		$this->property_is_active = $property_is_active;
	}


}

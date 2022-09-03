<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

#[DataModel_Definition(
	name: 'index_internal_property_group_word',
	database_table_name: 'fulltext_internal_property_groups_words'
)]
abstract class Core_Fulltext_Index_Internal_PropertyGroup_Word extends Fulltext_Index_Word {
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $group_is_active = false;

	
	public function getGroupIsActive() : bool
	{
		return $this->group_is_active;
	}
	
	public function setGroupIsActive( string $group_is_active ) : void
	{
		$this->group_is_active = $group_is_active;
	}
	
	
}

<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;

#[DataModel_Definition(
	name: '',
	database_table_name: '',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id']
)]
abstract class Core_Entity_Basic extends DataModel
{
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
	)]
	protected int $id = 0;
	
	
	
	public static function exists( int $id ) : bool
	{
		return (bool)static::dataFetchCol(['id'], ['id'=>$id]);
	}
	
	public static function getEntityType() : string
	{
		return static::getDataModelDefinition(static::class)->getModelName();
	}
	
	
}
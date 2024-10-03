<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use JetApplication\NumberSeries_Entity_Interface;

#[DataModel_Definition(
	name: '',
	database_table_name: '',
	id_controller_class: DataModel_IDController_Passive::class
)]
abstract class Core_NumberSeries_Counter extends DataModel
{
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_id: true,
	)]
	protected string $entity = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_id: true,
	)]
	protected string $shop_key = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $count = 0;
	
	abstract public static function generate( NumberSeries_Entity_Interface $entity, int $pad ) : string;
}
<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Form_Definition;
use Jet\Form_Field;


#[DataModel_Definition(
	name: 'suppliers',
	database_table_name: 'suppliers',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id']
)]
abstract class Core_Supplier extends DataModel {

	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
	)]
	protected int $id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Internal name:',
	)]
	protected string $internal_name = '';
	
	
	protected static ?array $scope = null;
	public static function getScope() : array
	{
		if(static::$scope===null) {
			static::$scope = static::dataFetchPairs(
				select: [
					'id',
					'internal_name'
				], order_by: ['internal_name']);
		}
		return static::$scope;
	}


	public function getId() : int
	{
		return $this->id;
	}

	public function setId( int $id ) : void
	{
		$this->id = $id;
	}

	public function getInternalName() : string
	{
		return $this->internal_name;
	}

	public function setInternalName( string $internal_name ) : void
	{
		$this->internal_name = $internal_name;
	}


}
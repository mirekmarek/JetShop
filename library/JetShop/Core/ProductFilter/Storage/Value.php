<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\DataModel_Related_1toN;
use JetApplication\ProductFilter_Storage;


#[DataModel_Definition(
	name: 'product_filter_storage_value',
	database_table_name: 'product_filter_storage_value',
	parent_model_class: ProductFilter_Storage::class,
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: [
		'id_property_name' => 'id'
	]
)]
abstract class Core_ProductFilter_Storage_Value extends DataModel_Related_1toN
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true
	)]
	protected int $id = 0;
	
	#[DataModel_Definition(
		related_to: 'main.id',
		is_key: true
	)]
	protected int $storage_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $filter_key = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $filter_value_key = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $filter_value_context_id = 0;


	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $value = 0;

	public function getArrayKeyValue() : string
	{
		return $this->id;
	}
	
	public function getId() : int
	{
		return $this->id;
	}
	
	public function getStorageId() : int
	{
		return $this->storage_id;
	}
	
	public function setFilterKey( string $value ) : void
	{
		$this->filter_key = $value;
	}
	
	public function getFilterKey() : string
	{
		return $this->filter_key;
	}
	
	public function getFilterValueKey(): string
	{
		return $this->filter_value_key;
	}
	
	public function setFilterValueKey( string $filter_value_key ): void
	{
		$this->filter_value_key = $filter_value_key;
	}
	
	
	
	public function setFilterValueContextId( int $value ) : void
	{
		$this->filter_value_context_id = $value;
	}
	
	public function getFilterValueContextId() : int
	{
		return $this->filter_value_context_id;
	}
	
	public function setValue( int $value ) : void
	{
		$this->value = $value;
	}
	
	public function getValue() : int
	{
		return $this->value;
	}

}

<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_Fetch_Instances;
use Jet\DataModel_IDController_AutoIncrement;

use JetApplication\EShopEntity_WithEShopRelation;
use JetApplication\ProductFilter;
use JetApplication\ProductFilter_Filter;
use JetApplication\ProductFilter_Storage_Value;

/**
 *
 */
#[DataModel_Definition(
	name: 'product_filter_storage',
	database_table_name: 'product_filter_storage',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: [
		'id_property_name' => 'id'
	]
)]
abstract class Core_ProductFilter_Storage extends EShopEntity_WithEShopRelation
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 100
	)]
	protected string $context_entity = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $context_entity_id = 0;

	/**
	 * @var ProductFilter_Storage_Value[]
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: ProductFilter_Storage_Value::class
	)]
	protected array $values = [];
	
	/**
	 * @param int|string $id
	 * @return static|null
	 */
	public static function get( int|string $id ) : static|null
	{
		return static::load( $id );
	}

	/**
	 * @noinspection PhpDocSignatureInspection
	 * @return static[]|DataModel_Fetch_Instances
	 */
	public static function getList() : iterable
	{
		$where = [];
		
		return static::fetchInstances( $where );
	}
	
	public function getId() : int
	{
		return $this->id;
	}

	public function setContextEntity( string $value ) : void
	{
		$this->context_entity = $value;
	}
	
	public function getContextEntity() : string
	{
		return $this->context_entity;
	}
	
	public function setContextEntityId( int $value ) : void
	{
		$this->context_entity_id = $value;
	}
	
	public function getContextEntityId() : int
	{
		return $this->context_entity_id;
	}
	
	
	public static function getStorage( ProductFilter $filter ) : static
	{
		$context_entity = $filter->getContextEntity();
		$context_entity_id = $filter->getContextEntityId();
		
		$eshop = $filter->getEshop();
		$where = $eshop->getWhere();
		
		$where[] = 'AND';
		$where[] = [
			'context_entity' => $context_entity,
			'AND',
			'context_entity_id' => $context_entity_id
		];
		
		$storage = static::load( $where );
		if(!$storage) {
			$storage = new static();
			$storage->setContextEntity( $context_entity );
			$storage->setContextEntityId( $context_entity_id );
			$storage->setEshop( $filter->getEshop() );
		}

		return $storage;
	}
	
	
	public static function loadFilter( ProductFilter $filter ) : bool
	{
		$storage = static::getStorage( $filter );
		if($storage->getIsNew()) {
			return false;
		}
		
		foreach($filter->getFilters() as $filter) {
			$filter->load( $storage );
		}
		
		return true;
	}
	
	public static function saveFilter( ProductFilter $filter ) : static
	{
		$storage = static::getStorage( $filter );
		
		foreach($filter->getFilters() as $filter) {
			if($filter->getIsActive()) {
				$filter->save( $storage );
			} else {
				$storage->unsetAllValues( $filter );
			}
		}
		
		$storage->save();
		
		return $storage;
	}
	

	public function setValue( ProductFilter_Filter $filter, string $value_key, int $filter_value_context_id=0, int $value=0 ) : void
	{
		$v_i = null;
		foreach($this->values as $v) {
			if(
				$v->getFilterKey()==$filter->getKey() &&
				$v->getFilterValueKey()==$value_key &&
				$v->getFilterValueContextId()==$filter_value_context_id
			) {
				$v_i = $v;
				break;
			}
		}
		
		if(!$v_i) {
			$v_i = new ProductFilter_Storage_Value();
			$v_i->setFilterKey( $filter->getKey() );
			$v_i->setFilterValueKey( $value_key );
			$v_i->setFilterValueContextId( $filter_value_context_id );
			$v_i->setValue( $value );
			$this->values[] = $v_i;
			return;
		}
		
		$v_i->setValue( $value );
	}
	
	public function unsetValue( ProductFilter_Filter $filter, string $value_key, int $filter_value_context_id=0 ) : void
	{
		foreach($this->values as $i=>$v) {
			if(
				$v->getFilterKey()==$filter->getKey() &&
				$v->getFilterValueKey()==$value_key &&
				$v->getFilterValueContextId()==$filter_value_context_id
			) {
				unset($this->values[$i]);
				$v->delete();
				break;
			}
		}
	}
	
	public function getValue( ProductFilter_Filter $filter, string $value_key, int $filter_value_context_id=0 ) : ?int
	{
		foreach($this->values as $i=>$v) {
			if(
				$v->getFilterKey()==$filter->getKey() &&
				$v->getFilterValueKey()==$value_key &&
				$v->getFilterValueContextId()==$filter_value_context_id
			) {
				return $v->getValue();
			}
		}
		
		return null;
	}
	
	public function getAllValues( ProductFilter_Filter $filter ) : array
	{
		$values = [];
		foreach($this->values as $i=>$v) {
			if(
				$v->getFilterKey()==$filter->getKey()
			) {
				$values[$v->getFilterValueContextId()][$v->getFilterValueKey()][] = $v->getValue();
			}
		}
		
		return $values;
	}
	
	
	public function getValues( ProductFilter_Filter $filter, string $value_key ) : array
	{
		$values = [];
		foreach($this->values as $i=>$v) {
			if(
				$v->getFilterKey()==$filter->getKey() &&
				$v->getFilterValueKey()==$value_key
			) {
				$values[$v->getFilterValueContextId()][] = $v->getValue();
			}
		}
		
		return $values;
	}
	
	public function unsetAllValues( ProductFilter_Filter $filter ) : void
	{
		foreach($this->values as $i=>$v) {
			if(
				$v->getFilterKey()==$filter->getKey()
			) {
				unset( $this->values[$i] );
				$v->delete();
			}
		}
	}
	
	public function unsetValues( ProductFilter_Filter $filter, string $value_key ) : void
	{
		foreach( $this->values as $i => $v ) {
			if(
				$v->getFilterKey() == $filter->getKey() &&
				$v->getFilterValueKey() == $value_key
			) {
				unset( $this->values[$i] );
				$v->delete();
			}
		}
	}

	public function setValues( ProductFilter_Filter $filter, string $value_key, array $values ) : void
	{
		
		foreach($values as $filter_value_context_id=>$_values) {
			foreach($_values as $value) {
				$v = new ProductFilter_Storage_Value();
				$v->setFilterKey( $filter->getKey() );
				$v->setFilterValueKey( $value_key );
				$v->setFilterValueContextId( $filter_value_context_id );
				$v->setValue( $value );
				
				$this->values[] = $v;
			}
		}
		
	}
	
}

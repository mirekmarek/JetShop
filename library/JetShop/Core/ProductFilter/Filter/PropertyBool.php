<?php
namespace JetShop;

use JetApplication\Product_Parameter_Value;
use JetApplication\ProductFilter_Filter;
use JetApplication\ProductFilter_Storage;

abstract class Core_ProductFilter_Filter_PropertyBool extends ProductFilter_Filter
{
	protected array $property_rules = [];
	
	public function getKey(): string
	{
		return 'property_bool';
	}
	
	public function addPropertyRule( int $property_id, bool $value ) : void
	{
		$this->is_active = true;
		$this->property_rules[$property_id] = $value;
	}
	
	public function removePropertyRule( int $property_id ) : void
	{
		if(isset($this->property_rules[$property_id])) {
			unset( $this->property_rules[$property_id] );
		}
		if(!count($this->property_rules)) {
			$this->is_active = false;
		}
	}
	
	public function getPropertyRules() : array
	{
		return $this->property_rules;
	}
	
	public function getPropertyRule( int $property_id ) : ?bool
	{
		return $this->property_rules[$property_id]??null;
	}
	
	public function filter(): void
	{
		
		$where = [];
		if($this->previous_filter_result!==null) {
			$where['product_id'] = $this->previous_filter_result;
			$where[] = 'AND';
		}
		
		$where['property_id'] = array_keys( $this->property_rules );
		
		$data = Product_Parameter_Value::dataFetchAll(
			select: [
				'product_id',
				'property_id',
				'value'
			],
			where: $where,
			raw_mode: true
		);
		
		$this->filter_result = [];
		foreach($data as $d) {
			$product_id = (int)$d['product_id'];
			
			if(
				$this->previous_filter_result &&
				!in_array($product_id, $this->previous_filter_result)
			) {
				continue;
			}
			
			
			$property_id = (int)$d['property_id'];
			
			if($this->property_rules[$property_id]==(bool)$d['value']) {
				$this->filter_result[] = $product_id;
			}
		}
		
	}
	
	
	public function load( ProductFilter_Storage $storage ): void
	{
		$values = $storage->getValues( $this, 'properties' );
		
		foreach($values as $property_id=>$value ) {
			$this->addPropertyRule( $property_id, (bool)$value[0] );
		}
	}
	
	public function save( ProductFilter_Storage $storage ): void
	{
		$values = [];
		foreach( $this->property_rules as $property_id => $value ) {
			$values[$property_id] = [$value?1:0];
		}
		
		$storage->unsetValues( $this, 'properties' );
		$storage->setValues( $this, 'properties', $values );
	}
	
}
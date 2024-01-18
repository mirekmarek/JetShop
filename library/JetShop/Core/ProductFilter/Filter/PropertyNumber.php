<?php
namespace JetShop;

use JetApplication\Product_Parameter_Value;
use JetApplication\ProductFilter_Filter;
use JetApplication\ProductFilter_Storage;

abstract class Core_ProductFilter_Filter_PropertyNumber extends ProductFilter_Filter
{
	protected array $property_rules = [];
	
	public function getKey(): string
	{
		return 'property_number';
	}
	
	public function getPropertyRules(): array
	{
		return $this->property_rules;
	}
	
	
	
	public function addPropertyRule( int $property_id, ?float $min, ?float $max ) : void
	{
		if($min!==null) {
			$this->setPropertyRuleMin( $property_id, $min );
		}
		if($max!==null) {
			$this->setPropertyRuleMax( $property_id, $max );
		}
		
	}
	
	
	public function setPropertyRuleMin( int $property_id, float $min ) : void
	{
		if(!isset($this->property_rules[ $property_id ])) {
			$this->property_rules[ $property_id ] = [];
		}
		
		$this->property_rules[ $property_id ]['min'] = round($min*1000);
		
		$this->is_active = true;
	}
	
	public function setPropertyRuleMax( int $property_id, float $max ) : void
	{
		if(!isset($this->property_rules[ $property_id ])) {
			$this->property_rules[ $property_id ] = [];
		}
		
		$this->property_rules[ $property_id ]['max'] = round($max*1000);
		
		$this->is_active = true;
	}

	
	public function getPropertyRuleMin( int $property_id ) : ?float
	{
		if(!isset($this->property_rules[ $property_id ]['min'])) {
			return null;
		}
		if($this->property_rules[ $property_id ]['min']===null) {
			return null;
		}
		
		return $this->property_rules[ $property_id ]['min']/1000;
	}
	
	public function getPropertyRuleMax( int $property_id ) : ?float
	{
		if(!isset($this->property_rules[ $property_id ]['max'])) {
			return null;
		}
		if($this->property_rules[ $property_id ]['max']===null) {
			return null;
		}
		
		return $this->property_rules[ $property_id ]['max']/1000;
	}
	
	
	
	public function unsetPropertyRule( int $property_id ) : void
	{
		if(!isset($this->property_rules[ $property_id ])) {
			return;
		}
		unset($this->property_rules[ $property_id ]);
		
		if(!$this->property_rules) {
			$this->is_active = false;
		}
	}
	
	
	public function unsetPropertyRuleMin( int $property_id ) : void
	{
		if(!isset($this->property_rules[ $property_id ])) {
			return;
		}
		unset($this->property_rules[ $property_id ]['min']);
		if(!$this->property_rules[ $property_id ]) {
			unset($this->property_rules[ $property_id ]);
		}
		
		if(!$this->property_rules) {
			$this->is_active = false;
		}
	}
	
	public function unsetPropertyRuleMax( int $property_id ) : void
	{
		if(!isset($this->property_rules[ $property_id ])) {
			return;
		}
		unset($this->property_rules[ $property_id ]['max']);
		if(!$this->property_rules[ $property_id ]) {
			unset($this->property_rules[ $property_id ]);
		}
		
		if(!$this->property_rules) {
			$this->is_active = false;
		}
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
			
			$value = (int)$d['value'];
			
			$rule = $this->property_rules[$property_id];
			$min = $rule['min']??null;
			$max = $rule['max']??null;
			
			if(
				$min!==null &&
				$min>$value
			) {
				continue;
			}
			if(
				$max!==null &&
				$value>$max
			) {
				continue;
			}
			
			$this->filter_result[] = $product_id;
		}
		
	}
	
	public function load( ProductFilter_Storage $storage ): void
	{
		$values = $storage->getAllValues( $this );
		
		
		foreach($values as $property_id=>$_r) {
			$this->addPropertyRule( $property_id, $_r['min'][0]??null, $_r['max'][0]??null );
		}
		
	}
	
	public function save( ProductFilter_Storage $storage ): void
	{
		$storage->unsetAllValues( $this );
		
		foreach($this->property_rules as $property_id=>$rule) {
			$min = $rule['min']??null;
			$max = $rule['max']??null;
			
			if($min!==null) {
				$storage->setValue(
					filter: $this,
					value_key: 'min',
					filter_value_context_id: $property_id,
					value: $min
				);
			}
			
			if($max!==null) {
				$storage->setValue(
					filter: $this,
					value_key: 'max',
					filter_value_context_id: $property_id,
					value: $max
				);
			}
			
		}
		
	}
	
}
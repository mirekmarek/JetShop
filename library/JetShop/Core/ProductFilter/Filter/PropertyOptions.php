<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Product_Parameter_Value;
use JetApplication\ProductFilter_Filter;
use JetApplication\ProductFilter_Filter_PropertyOptions_Property;
use JetApplication\ProductFilter_Storage;
use JetApplication\Property_Options_Option;

abstract class Core_ProductFilter_Filter_PropertyOptions extends ProductFilter_Filter
{
	
	/**
	 * @var ProductFilter_Filter_PropertyOptions_Property[]
	 */
	protected array $properties = [];
	
	public function getKey(): string
	{
		return 'property_options';
	}
	
	public function initOptions( array $property_ids ) : void
	{
		$options = Property_Options_Option::dataFetchAll(
			select: [
				'id',
				'property_id'
			],
			where: [
				'property_id' => $property_ids
			],
			raw_mode: true
		);
		
		$map = [];
		foreach($options as $o) {
			$option_id = (int)$o['id'];
			$property_id = (int)$o['property_id'];
			
			if(!isset($map[$property_id])) {
				$map[$property_id] = [];
			}
			
			$map[$property_id][] = $option_id;
		}
		
		foreach($map as $property_id=>$option_ids) {
			if($option_ids) {
				if(!isset($this->properties[$property_id])) {
					$this->properties[$property_id] = new ProductFilter_Filter_PropertyOptions_Property( $this, $property_id, $option_ids );
				}
			}
		}
	}
	
	public function setSelectedOptions( int $property_id, array $selected_option_ids ) : void
	{
		if($selected_option_ids) {
			$this->is_active = true;
			if(!isset($this->properties[$property_id])) {
				$this->properties[$property_id] = new ProductFilter_Filter_PropertyOptions_Property( $this, $property_id, $selected_option_ids );
			}
			$this->properties[$property_id]->setSelectedOptions( $selected_option_ids );
		}
	}
	
	public function getOptionIsSelected( int $property_id, int $option_id ) : bool
	{
		if(!isset( $this->properties[$property_id] )) {
			return false;
		}
		
		return (bool)$this->properties[$property_id]?->getOption($option_id)?->getSelected();
	}
	
	public function getSelectedOptionIds( bool $hash_by_property ) : array
	{
		$res = [];
		
		foreach($this->properties as $property) {
			if(!$property->hasSelectedOption()) {
				continue;
			}
			
			if($hash_by_property) {
				$res[$property->getId()] = $property->getSelectedOptionIds();
			} else {
				$res = array_merge( $res, $property->getSelectedOptionIds() );
			}
		}
		
		return $res;
	}
	
	public function getFilteredProductsWithoutProperty( int $skip_property_id ) : array
	{
		$res = [];
		
		foreach($this->properties as $property_id=>$property) {
			if(
				$property_id!=$skip_property_id &&
				$property->hasSelectedOption()
			) {
				$res[] = $property->getFilteredProductIds();
			}
		}
		
		if(!count($res)) {
			return [];
		}
		
		if(count($res)==1) {
			return $res[0];
		}
		
		return call_user_func_array('array_intersect', $res);
	}
	
	public function selectOption( int $property_id, int $option_id ) : void
	{
		if(!isset($this->properties[$property_id])) {
			$this->properties[$property_id] = new ProductFilter_Filter_PropertyOptions_Property( $this, $property_id, [$option_id] );
		}
		
		$this->properties[$property_id]->selectOption( $option_id );
		
		$this->is_active = true;
	}
	
	public function unselectOption( int $property_id, int $option_id ) : void
	{
		if(isset($this->properties[$property_id])) {
			$this->properties[$property_id]->unselectOption( $option_id );
		}
		
		$this->is_active = false;
		foreach($this->properties as $property) {
			if($property->hasSelectedOption()) {
				$this->is_active = true;
				break;
			}
		}
	}
	
	public function filter(): void
	{
		
		$where = [];
		if($this->previous_filter_result!==null) {
			$where['product_id'] = $this->previous_filter_result;
			$where[] = 'AND';
		}
		
		$where['property_id'] = array_keys( $this->properties );

		$data = Product_Parameter_Value::dataFetchAll(
			select: [
				'product_id',
				'property_id',
				'value'
			],
			where: $where,
			raw_mode: true
		);

		foreach($data as $d) {
			$property_id = (int)$d['property_id'];
			$option_id = (int)$d['value'];
			$product_id = (int)$d['product_id'];
			
			if(
				$this->previous_filter_result &&
				!in_array($product_id, $this->previous_filter_result)
			) {
				continue;
			}
			
			$this->properties[$property_id]->addProductId( $option_id, $product_id );
		}
		
		foreach($this->properties as $property_id=>$property) {
			if(!count($property->getProductIds())) {
				unset( $this->properties[$property_id] );
			}
		}

		
		$selected_product_ids = [];
		foreach($this->properties as $property_id=>$property) {
			if($property->hasSelectedOption()) {
				$selected_product_ids[] = $property->getFilteredProductIds();
			}
		}
		
		
		if(!$selected_product_ids) {
			$this->filter_result = [];
			return;
		}
		
		if(count($selected_product_ids)==1) {
			$this->filter_result = $selected_product_ids[0];
			return;
		}
		
		
		$this->filter_result = call_user_func_array('array_intersect', $selected_product_ids);

	}
	
	public function load( ProductFilter_Storage $storage ): void
	{
		$values = $storage->getValues( $this, 'properties' );
		
		foreach($values as $property_id=>$options ) {
			$this->setSelectedOptions( $property_id, $options );
		}
	}
	
	public function save( ProductFilter_Storage $storage ): void
	{
		$values = [];
		foreach( $this->properties as $property_id=>$property ) {
			
			$selected = $property->getSelectedOptionIds();
			if($selected) {
				$values[$property_id] = $selected;
			}
		}
		
		$storage->unsetValues( $this, 'properties' );
		$storage->setValues( $this, 'properties', $values );
	}
}
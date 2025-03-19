<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Product_Parameter_InfoNotAvl;
use JetApplication\Product_Parameter_Value;
use JetApplication\Product_Parameter_TextValue;
use JetApplication\EShop;

abstract class Core_Product_Parameter {
	
	protected int $product_id;
	protected int $property_id;
	
	/**
	 * @var Product_Parameter_Value[][]
	 */
	protected static array $value_maps = [];
	
	/**
	 * @var Product_Parameter_TextValue[][]
	 */
	protected static array $text_value_maps = [];
	
	/**
	 * @var Product_Parameter_InfoNotAvl[][]
	 */
	protected static array $info_not_avl_maps = [];
	
	
	public function __construct( int $product_id, int $property_id )
	{
		$this->product_id = $product_id;
		$this->property_id = $property_id;
	}
	
	public function getPropertyValue() : ?Product_Parameter_Value
	{
		return $this->getParameterValuesMap()[$this->property_id][0]??null;
	}
	
	/**
	 * @return Product_Parameter_Value[]|null
	 */
	public function getPropertyValues() : ?array
	{
		$_values =  $this->getParameterValuesMap()[$this->property_id]??null;
		
		if(!$_values) {
			return null;
		}
		
		$values = [];
		foreach($_values as $v) {
			$values[$v->getValue()] = $v->getValue();
		}
		
		return $values;
	}
	
	
	public function getPropertyTextValue( EShop $eshop ) : string
	{
		$_values =  $this->getParameterTextValuesMap()[$this->property_id]??null;
		
		if(!$_values) {
			return '';
		}
		
		if(!isset( $_values[$eshop->getKey()])) {
			return '';
		}
		
		return $_values[$eshop->getKey()]->getText();
	}
	
	
	public function setPropertyValue( ?int $value ) : bool
	{
		$current_value = $this->getParameterValuesMap()[$this->property_id][0]??null;
		
		if($value===null) {
			if($current_value) {
				$current_value->delete();
				static::$value_maps[$this->product_id][$this->property_id];
				
				return true;
			}
			
			return false;
		}
		
		if(!$current_value) {
			$v = new Product_Parameter_Value();
			$v->setProductId( $this->product_id );
			$v->setPropertyId( $this->property_id );
			$v->setValue( $value );
			$v->save();
			
			static::$value_maps[$this->product_id][$this->property_id] = [$v];
			
			return true;
		}
		
		if($current_value->getValue()!=$value) {
			$current_value->setValue( $value );
			$current_value->save();
			
			return true;
		}
		
		return false;
	}
	
	public function setPropertyValues( null|array $values ) : bool
	{
		$current_values =  $this->getParameterValuesMap()[$this->property_id]??null;
		
		$updated = false;
		
		if($values===null) {
			
			if($current_values) {
				Product_Parameter_Value::dataDelete([
					'product_id' => $this->product_id,
					'AND',
					'property_id' => $this->property_id
				]);
				
				unset( static::$value_maps[$this->product_id][$this->property_id] );
				
				$updated = true;
			}
			
			return $updated;
		}
		
		if(!$current_values) {
			static::$value_maps[$this->product_id][$this->property_id] = [];
			
			foreach($values as $new_value) {
				$v = new Product_Parameter_Value();
				$v->setProductId( $this->product_id );
				$v->setPropertyId( $this->property_id );
				$v->setValue( $new_value );
				$v->save();
				
				static::$value_maps[$this->product_id][$this->property_id][] = $v;
			}
			
			return true;
		}
		
		$_current_values = [];
		
		foreach(static::$value_maps[$this->product_id][$this->property_id] as $i=>$current_value) {
			$_current_values[] = $current_value->getValue();
			if(!in_array($current_value->getValue(), $values)) {
				$current_value->delete();
				unset(static::$value_maps[$this->product_id][$this->property_id][$i]);
				$updated = true;
			}
		}
		
		foreach($values as $new_value) {
			if(!in_array($new_value, $_current_values)) {
				$v = new Product_Parameter_Value();
				$v->setProductId( $this->product_id );
				$v->setPropertyId( $this->property_id );
				$v->setValue( $new_value );
				$v->save();
				
				static::$value_maps[$this->product_id][$this->property_id][] = $v;
				
			}
		}
		

		return $updated;
	}
	
	public function setPropertyTextValue( EShop $eshop, string $text ) : bool
	{
		$current_value = $this->getParameterTextValuesMap()[$eshop->getKey()]??null;

		
		if(!$current_value) {
			$v = new Product_Parameter_TextValue();
			$v->setProductId( $this->product_id );
			$v->setPropertyId( $this->property_id );
			$v->setEshop( $eshop );
			$v->setText( $text );
			$v->save();
			
			static::$text_value_maps[$this->product_id][$this->property_id][$eshop->getKey()] = $v;
			
			return true;
		}
		
		if($current_value->getText()!=$text) {
			$current_value->setText( $text );
			$current_value->save();
			
			return true;
		}
		
		return false;
	}
	
	
	/**
	 * @return Product_Parameter_Value[][]
	 */
	protected function getParameterValuesMap() : array
	{
		
		$product_id = $this->product_id;
		
		if(!array_key_exists($product_id, static::$value_maps)) {
			static::$value_maps[$product_id] = Product_Parameter_Value::getForProduct( $product_id );
		}
		
		return static::$value_maps[$product_id];
	}
	
	
	/**
	 * @return Product_Parameter_TextValue[][]
	 */
	protected function getParameterTextValuesMap() : array
	{
		
		$product_id = $this->product_id;
		
		if(!array_key_exists($product_id, static::$text_value_maps)) {
			static::$text_value_maps[$product_id] = Product_Parameter_TextValue::getForProduct( $product_id );
		}
		
		return static::$text_value_maps[$product_id];
	}
	
	
	/**
	 * @return Product_Parameter_InfoNotAvl[]
	 */
	protected function getInfoNotAvlMap() : array
	{
		
		$product_id = $this->product_id;
		
		if(!array_key_exists($product_id, static::$info_not_avl_maps)) {
			static::$info_not_avl_maps[$product_id] = Product_Parameter_InfoNotAvl::getForProduct( $product_id );
		}
		
		return static::$info_not_avl_maps[$product_id];
	}
	
	public function getInfoNotAvl() : bool
	{
		$this->getInfoNotAvlMap();
		
		return isset(static::$info_not_avl_maps[$this->product_id][$this->property_id]);
	}
	
	public function setInfoNotAvl( bool $state ) : void
	{
		$this->getInfoNotAvlMap();
		
		if(!$state) {
			if(isset(static::$info_not_avl_maps[$this->product_id][$this->property_id])) {
				static::$info_not_avl_maps[$this->product_id][$this->property_id]->delete();
				unset( static::$info_not_avl_maps[$this->product_id][$this->property_id] );
			}
		} else {
			if(!isset(static::$info_not_avl_maps[$this->product_id][$this->property_id])) {
				$v = new Product_Parameter_InfoNotAvl();
				$v->setProductId( $this->product_id );
				$v->setPropertyId($this->property_id);
				$v->save();
				static::$info_not_avl_maps[$this->product_id][$this->property_id] = $v;
				
			}
			
		}
	}
	
}
<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Form;
use Jet\Form_Field_Float;
use Jet\Form_Field_Int;
use JetApplication\ProductFilter;
use JetApplication\Property_Type;
use JetApplication\EShop;


abstract class Core_Property_Type_Number extends Property_Type
{
	
	public function canBeVariantSelector() : bool
	{
		return false;
	}
	
	public function getProductParameterValue() : null|float|int
	{
		$value = $this->product_parameter->getPropertyValue();
		if($value===null) {
			return null;
		}
		
		$value = $value->getValue()/1000;
		
		if($this->property_model->getDecimalPlaces()==0) {
			return floor($value);
		}
		
		return $value;
	}
	
	
	public function getValueEditForm() : Form
	{
		
		if($this->property_model->getDecimalPlaces()==0) {
			$value = new Form_Field_Int('value', $this->property_model->getInternalName() );
		} else {
			$value = new Form_Field_Float('value', $this->property_model->getInternalName() );
			$value->setPlaces( $this->property_model->getDecimalPlaces() );
		}
		
		$value->setDefaultValue( $this->getProductParameterValue() );
		$value->setFieldValueCatcher( function( $value ) {
			if($value==="") {
				$value = null;
			}
			if($value!==null) {
				$value = $value*1000;
			}
			$this->product_parameter->setPropertyValue( $value );
		} );
		
		
		return $this->_getValueEditForm( [$value] );
	}
	
	public function getProductFilterEditForm( ProductFilter $filter ) : ?Form
	{
		$property_id = $this->property_id;
		$filter = $filter->getPropertyNumberFilter();
		
		$min = new Form_Field_Float('/property/'.$property_id.'/min', "Min:" );
		if( ($min_value=$filter->getPropertyRuleMin( $property_id ))!==null ) {
			$min->setDefaultValue( $min_value/1000 );
		}
		$min->setFieldValueCatcher( function( $value ) use ($filter, $property_id) {
			if($value) {
				$filter->setPropertyRuleMin( $property_id, round($value*1000) );
			} else {
				$filter->unsetPropertyRuleMin( $property_id );
			}
		} );
		
		$max = new Form_Field_Float('/property/'.$property_id.'/max', "Max:" );
		if( ($max_value=$filter->getPropertyRuleMax( $property_id ))!==null ) {
			$max->setDefaultValue( $max_value/1000 );
		}
		$max->setFieldValueCatcher( function( $value ) use ($filter, $property_id) {
			if($value) {
				$filter->setPropertyRuleMax( $property_id, round($value*1000) );
			} else {
				$filter->unsetPropertyRuleMax( $property_id );
			}
		} );

		
		return new Form('', [
			$min,
			$max
		]);
	}
	
	public function setupForm( Form $form ) : void
	{
		foreach($form->getFields() as $f) {
			if(str_ends_with($f->getName(),'/bool_yes_description')) {
				$form->removeField( $f->getName() );
			}
		}
	}
	
	public function getProductDetailDisplayValue( ?EShop $eshop=null ): int|float
	{
		if(($dp=$this->property_model->getDecimalPlaces())) {
			return (float)$this->getProductParameterValue();
		} else {
			return (int)$this->getProductParameterValue();
		}
	}
}
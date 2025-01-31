<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Form;
use Jet\Form_Field_Checkbox;
use JetApplication\ProductFilter;
use JetApplication\Property_Type;
use JetApplication\EShop;

abstract class Core_Property_Type_Bool extends Property_Type
{
	
	public function canBeVariantSelector(): bool
	{
		return false;
	}
	
	public function getProductParameterValue(): ?bool
	{
		$value = $this->product_parameter->getPropertyValue();
		if( $value === null ) {
			return null;
		}
		
		return $value->getValue() == 1;
	}
	
	public function getValueEditForm(): Form
	{
		
		$value = new Form_Field_Checkbox( 'value', $this->property_model->getInternalName() );
		$value->setDefaultValue( $this->getProductParameterValue() );
		$value->setFieldValueCatcher( function( $value ) {
			$this->product_parameter->setPropertyValue( $value ? 1 : 0 );
		} );
		
		return $this->_getValueEditForm( [$value] );
		
	}
	
	public function setupForm( Form $form ): void
	{
		$form->removeField( 'decimal_places' );
	}
	
	public function getProductFilterEditForm( ProductFilter $filter ): ?Form
	{
		$property_id = $this->property_id;
		$filter = $filter->getPropertyBoolFilter();
		
		$checked = new Form_Field_Checkbox( '/property/' . $property_id . '/checked', $this->property_model->getInternalName() );
		$checked->setDefaultValue( $filter->getPropertyRule( $property_id ) );
		$checked->setFieldValueCatcher( function( $value ) use ( $filter, $property_id ) {
			if( $value ) {
				$filter->addPropertyRule( $property_id, true );
			} else {
				$filter->removePropertyRule( $property_id );
			}
		} );
		
		return new Form( '', [
			$checked
		] );
	}
	
	public function getProductDetailDisplayValue( ?EShop $eshop=null ): bool
	{
		return (bool)$this->getProductParameterValue();
	}
}
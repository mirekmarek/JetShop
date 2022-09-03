<?php
namespace JetShop;
use Jet\Form;
use Jet\Form_Field_Checkbox;


abstract class Core_Property_Bool_Value extends Property_Value
{
	public function getValueEditForm( Product_Parameter $product_value ) : Form
	{

		$value = new Form_Field_Checkbox('value', $this->property->getShopData()->getLabel() );
		$value->setDefaultValue( $product_value->getRawValue() );
		$value->setFieldValueCatcher( function( $value ) use ($product_value) {
			$product_value->setRawValue( $value?1:0 );
		} );


		$information_in_not_available = new Form_Field_Checkbox(
			'information_in_not_available',
			'Information is not available',
		);
		$information_in_not_available->setDefaultValue( $product_value->isInformationIsNotAvailable() );
		$information_in_not_available->setFieldValueCatcher( function( $value ) use ($product_value) {
			$product_value->setInformationIsNotAvailable( $value );
		} );


		return new Form('properties_edit_form', [
			$value,
			$information_in_not_available
		]);

	}


}
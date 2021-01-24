<?php
namespace JetShop;
use Jet\Form;
use Jet\Form_Field_Checkbox;


abstract class Core_Parametrization_Property_Value_Bool extends Parametrization_Property_Value
{
	public function getValueEditForm( Product_ParametrizationValue $product_value ) : Form
	{

		$value = new Form_Field_Checkbox('value', $this->property->getShopData()->getLabel(), $product_value->getRawValue() );
		$value->setCatcher( function( $value ) use ($product_value) {
			$product_value->setRawValue( $value?1:0 );
		} );


		$information_in_not_available = new Form_Field_Checkbox(
			'information_in_not_available',
			'Information is not available',
			$product_value->isInformationIsNotAvailable()
		);
		$information_in_not_available->setCatcher( function( $value ) use ($product_value) {
			$product_value->setInformationIsNotAvailable( $value );
		} );


		return new Form('properties_edit_form', [
			$value,
			$information_in_not_available
		]);

	}


}
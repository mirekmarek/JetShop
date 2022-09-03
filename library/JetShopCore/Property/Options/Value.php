<?php
namespace JetShop;
use Jet\Form;
use Jet\Form_Field;
use Jet\Form_Field_Checkbox;
use Jet\Form_Field_MultiSelect;

abstract class Core_Property_Options_Value extends Property_Value
{

	public function getValueEditForm( Product_Parameter $product_value ) : Form
	{

		$selected_options = [];
		if($product_value->getRawValue()) {
			$selected_options = explode(',', $product_value->getRawValue());
		}

		$options = [];
		foreach($this->property->getOptions() as $o) {
			$options[$o->getId()] = $o->getShopData()->getFilterLabel();
		}

		$value = new Form_Field_MultiSelect('value', $this->property->getShopData()->getLabel() );
		$value->setDefaultValue( $selected_options );
		$value->setSelectOptions( $options );
		$value->setFieldValueCatcher( function( $v ) use ($product_value, $value) {
			$product_value->setRawValue( implode(',', $v) );
		} );
		$value->setErrorMessages([
			Form_Field::ERROR_CODE_INVALID_VALUE => ' '
		]);


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
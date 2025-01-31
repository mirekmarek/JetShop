<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Form;
use Jet\Form_Field;
use Jet\Form_Field_Checkbox;
use Jet\Form_Field_MultiSelect;
use JetApplication\ProductFilter;
use JetApplication\Property_Options_Option_EShopData;
use JetApplication\Property_Type;
use JetApplication\EShops;
use JetApplication\EShop;

abstract class Core_Property_Type_Options extends Property_Type
{
	
	public function canBeVariantSelector() : bool
	{
		return true;
	}
	
	public function getProductParameterValue() : array
	{
		$value = $this->product_parameter->getPropertyValues();
		if(!$value) {
			return [];
		}
		
		return $value;
	}
	
	protected function getOptionsScope() : array
	{
		$options = [];
		foreach($this->property_model->getOptions() as $o) {
			$options[$o->getId()] = $o->getInternalName();
		}
		
		return $options;
		
	}
	
	
	public function getValueEditForm() : Form
	{
		
		$value = new Form_Field_MultiSelect('value', $this->property_model->getInternalName() );
		$value->setDefaultValue( $this->getProductParameterValue() );
		$value->setSelectOptions( $this->getOptionsScope() );
		$value->setFieldValueCatcher( function( $v ) use ($value) {
			if(!$v) {
				$v = null;
			}
			$this->product_parameter->setPropertyValues( $v );
		} );
		$value->setErrorMessages([
			Form_Field::ERROR_CODE_INVALID_VALUE => ' '
		]);
		
		return $this->_getValueEditForm( [$value] );
	}
	
	public function getProductFilterEditForm( ProductFilter $filter ) : ?Form
	{
		$fields = [];
		$property_id = $this->property_model->getId();
		$filter = $filter->getPropertyOptionsFilter();
		
		foreach($this->getOptionsScope() as  $option_id=>$option_label) {
			$option = new Form_Field_Checkbox('/property/'.$property_id.'/option_'.$option_id, $option_label );
			$option->setDoNotTranslateLabel( true );
			
			$option->setDefaultValue( $filter->getOptionIsSelected( $property_id, $option_id ) );
			
			$option->setFieldValueCatcher( function( $value ) use ($filter, $property_id, $option_id) {
				if($value) {
					$filter->selectOption( $property_id, $option_id );
				} else {
					$filter->unselectOption( $property_id, $option_id );
				}
			} );
			
			$fields[] = $option;
		}
		
		return new Form('', $fields);
	}
	
	public function setupForm( Form $form ) : void
	{
		$form->removeField('decimal_places');
		
		foreach($form->getFields() as $f) {
			if(str_ends_with($f->getName(),'/bool_yes_description')) {
				$form->removeField( $f->getName() );
			}
		}
	}
	
	/**
	 * @param EShop|null $eshop
	 * @return Property_Options_Option_EShopData[]
	 */
	public function getProductDetailDisplayValue( ?EShop $eshop=null ): array
	{
		$eshop = $eshop?:EShops::getCurrent();
		
		$values = $this->getProductParameterValue();
		if(!$values) {
			return [];
		}
		
		return Property_Options_Option_EShopData::getActiveList( $values, $eshop, ['priority'] );
	}
	
	
}
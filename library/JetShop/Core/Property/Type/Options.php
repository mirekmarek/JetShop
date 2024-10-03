<?php
namespace JetShop;

use Jet\Form;
use Jet\Form_Field;
use Jet\Form_Field_Checkbox;
use Jet\Form_Field_MultiSelect;
use JetApplication\ProductFilter;
use JetApplication\Property_Options_Option_ShopData;
use JetApplication\Property_Type;
use JetApplication\Shops;
use JetApplication\Shops_Shop;

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
		
		foreach( Shops::getList() as $shop ) {
			$shop_key = $shop->getKey();
			
			$form->removeField('/shop_data/'.$shop_key.'/bool_yes_description');
		}
		
	}
	
	/**
	 * @param Shops_Shop|null $shop
	 * @return Property_Options_Option_ShopData[]
	 */
	public function getProductDetailDisplayValue( ?Shops_Shop $shop=null ): array
	{
		$shop = $shop?:Shops::getCurrent();
		
		$values = $this->getProductParameterValue();
		if(!$values) {
			return [];
		}
		
		return Property_Options_Option_ShopData::getActiveList( $values, $shop, ['priority'] );
	}
	
	
}
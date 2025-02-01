<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Form;
use Jet\Form_Field_Input;
use JetApplication\ProductFilter;
use JetApplication\Property_Type;
use JetApplication\EShops;
use JetApplication\EShop;


abstract class Core_Property_Type_Text extends Property_Type
{
	
	
	public function canBeVariantSelector() : bool
	{
		return false;
	}
	
	public function canBeFilter() : bool
	{
		return false;
	}
	
	
	public function getProductParameterValue() : null
	{
		return null;
	}
	
	public function getProductParameterTextValue( EShop $eshop ) : string
	{
		return $this->product_parameter->getPropertyTextValue( $eshop );
	}
	
	
	public function getValueEditForm() : Form
	{
		$fields = [];
		foreach( EShops::getList() as $eshop) {
			$field = new Form_Field_Input( $eshop->getKey(), '' );
			
			$field->setDefaultValue( $this->getProductParameterTextValue( $eshop ) );
			$field->setFieldValueCatcher( function( $v ) use ($eshop) {
				$this->product_parameter->setPropertyTextValue( $eshop, $v );
			} );
			
			
			$fields[] = $field;
		}
		
		return $this->_getValueEditForm( $fields );
	}
	
	public function getProductFilterEditForm( ProductFilter $filter ) : ?Form
	{
		return null;
	}
	
	public function setupForm( Form $form ) : void
	{
		$form->removeField('decimal_places');
		
		foreach($form->getFields() as $f) {
			if(
				str_ends_with($f->getName(),'/bool_yes_description') ||
				str_ends_with($f->getName(),'/units')
			) {
				$form->removeField( $f->getName() );
			}
		}
		
	}
	
	
	public function getProductDetailDisplayValue( ?EShop $eshop=null ): string
	{
		$eshop = $eshop?:EShops::getCurrent();
		
		return $this->getProductParameterTextValue( $eshop );
	}
	
}
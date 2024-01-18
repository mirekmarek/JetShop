<?php
namespace JetShop;

use Jet\Form;
use Jet\Form_Field_Input;
use JetApplication\ProductFilter;
use JetApplication\Property_Type;
use JetApplication\Shops;
use JetApplication\Shops_Shop;


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
	
	public function getProductParameterTextValue( Shops_Shop $shop ) : string
	{
		return $this->product_parameter->getPropertyTextValue( $shop );
	}
	
	
	public function getValueEditForm() : Form
	{
		$fields = [];
		foreach(Shops::getList() as $shop) {
			$field = new Form_Field_Input( $shop->getKey(), '' );
			
			$field->setDefaultValue( $this->getProductParameterTextValue( $shop ) );
			$field->setFieldValueCatcher( function( $v ) use ($shop) {
				$this->product_parameter->setPropertyTextValue( $shop, $v );
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
		foreach( Shops::getList() as $shop ) {
			$form->removeField('/shop_data/'.$shop->getKey().'/bool_yes_description');
			$form->removeField('/shop_data/'.$shop->getKey().'/units');
			$form->removeField('/shop_data/'.$shop->getKey().'/url_param');
		}
	}
	
	
	public function getProductDetailDisplayValue( ?Shops_Shop $shop=null ): string
	{
		$shop = $shop?:Shops::getCurrent();
		
		return $this->getProductParameterTextValue( $shop );
	}
	
}
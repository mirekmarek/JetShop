<?php
namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\Form;
use Jet\Form_Field_Float;
use Jet\Form_Field_Hidden;
use Jet\Form_Field_Input;
use Jet\Form_Field_Int;
use Jet\Form_Field_Select;
use Jet\Tr;
use JetApplication\Shops;


trait Product_Set  {
	
	protected ?Form $_set_setup_form = null;
	
	protected ?Form $_set_add_item_form = null;
	
	protected function _setupForm_set( Form $form ) : void
	{
		if(!$this->isSet()) {
			return;
		}
		$form->removeField('internal_name_of_variant');
		
		foreach( Shops::getList() as $shop ) {
			$form->removeField( '/shop_data/'.$shop->getKey().'/variant_name' );
			$form->field('/shop_data/'.$shop->getKey().'/standard_price')->setIsReadonly( true );
			$form->field('/shop_data/'.$shop->getKey().'/price')->setIsReadonly( true );
			$form->field('/shop_data/'.$shop->getKey().'/in_stock_qty')->setIsReadonly( true );
			$form->field('/shop_data/'.$shop->getKey().'/length_of_delivery')->setIsReadonly( true );
			$form->field('/shop_data/'.$shop->getKey().'/available_from')->setIsReadonly( true );
		}
	}
	
	public function getSetSetupForm() : Form
	{
		if(!$this->_set_setup_form) {
			$this->_set_setup_form = new Form('set_setup_form', []);
			
			foreach($this->getSetItems() as $set_item) {
				
				
				$count = new Form_Field_Int('/p'.$set_item->getItemProductId().'/count', 'Count' );
				$count->setDefaultValue( $set_item->getCount() );
				$count->setFieldValueCatcher( function(int $value) use ($set_item) : void {
					$set_item->setCount($value);
				} );
				$sort_order = new Form_Field_Int('/p'.$set_item->getItemProductId().'/sort_order', 'Sort order' );
				$sort_order->setDefaultValue( $set_item->getSortOrder() );
				$sort_order->setFieldValueCatcher( function(int $value) use ($set_item) : void {
					$set_item->setSortOrder( $value );
				} );
				
				$this->_set_setup_form->addField($count);
				$this->_set_setup_form->addField($sort_order);
				
			}
			
			foreach( Shops::getList() as $shop ) {
				$discount_type = new Form_Field_Select(
					name: '/set_price/'.$shop->getKey().'/discount_type',
					label: 'Discount type:'
				);
				$discount_type->setDefaultValue( $this->getShopData($shop)->getSetDiscountType() );
				$discount_type->setSelectOptions([
					Product::SET_DISCOUNT_NONE => Tr::_('None'),
					Product::SET_DISCOUNT_PERCENT => Tr::_('Percent'),
					Product::SET_DISCOUNT_NOMINAL => Tr::_('Nominal')
				]);
				$discount_type->setErrorMessages([
					Form_Field_Input::ERROR_CODE_INVALID_VALUE => 'Invalid value'
				]);
				$discount_type->setFieldValueCatcher( function() use ($shop, $discount_type) {
					$this->getShopData($shop)->setSetDiscountType( $discount_type->getValue() );
				} );
				$this->_set_setup_form->addField( $discount_type );
				
				
				
				
				$discount_value = new Form_Field_Float(
					name: '/set_price/'.$shop->getKey().'/discount_value',
					label: 'Discount value:'
				);
				$discount_value->setDefaultValue(  $this->getShopData($shop)->getSetDiscountValue()  );
				$discount_value->setFieldValueCatcher( function() use ($shop, $discount_value) {
					$this->getShopData($shop)->setSetDiscountValue( $discount_value->getValue() );
				} );
				$this->_set_setup_form->addField( $discount_value );
				
			}
			
			
			
			if(!$this->isEditable()) {
				$this->_set_setup_form->setIsReadonly();
			}
			
		}
		
		return $this->_set_setup_form;
	}
	
	public function catchSetSetupForm() : bool
	{
		if(!$this->isEditable()) {
			return false;
		}
		
		$edit_form = $this->getSetSetupForm();
		
		if(!$edit_form->catch()) {
			return false;
		}
		
		$this->actualizeSet();
		
		return true;
	}
	
	
	public function getSetAddItemForm() : Form
	{
		if(!$this->_set_add_item_form) {
			
			$product_id = new Form_Field_Hidden('product_id');
			$product_id->setValidator(function( Form_Field_Hidden $field ) {
				$id = (int)$field->getValue();
				
				if(!$id) {
					return false;
				}
				
				$product_type = static::getProductType($id);
				if(!$product_type) {
					return false;
				}
				
				if(
					$product_type == static::PRODUCT_TYPE_VARIANT_MASTER ||
					$product_type == static::PRODUCT_TYPE_SET
				) {
					return false;
				}
				
				return true;
			});
			
			$this->_set_add_item_form = new Form('set_add_item_form', [$product_id]);
		}
		
		return $this->_set_add_item_form;
	}
	
	public function catchSetAddItemForm() : bool
	{
		if(!$this->isEditable()) {
			return false;
		}
		
		$form = $this->getSetAddItemForm();
		if(!$form->catch()) {
			return false;
		}
		
		$id = $form->field('product_id')->getValue();
		
		$this->addSetItem( $id );
		
		return true;
	}
	
	
}
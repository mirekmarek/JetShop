<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Exception;

use Jet\Form;
use Jet\Form_Field_Float;
use Jet\Form_Field_Hidden;
use Jet\Form_Field_Input;
use Jet\Form_Field_Int;
use Jet\Form_Field_Select;
use Jet\Tr;
use JetApplication\Availabilities;
use JetApplication\EShops;
use JetApplication\Pricelists;
use JetApplication\Pricelist;
use JetApplication\Product_Availability;
use JetApplication\Product_Parameter_Value;
use JetApplication\Product_Price;
use JetApplication\Product_SetItem;
use JetApplication\Product;

trait Core_Product_Trait_Set {
	
	protected ?Form $_set_setup_form = null;
	
	protected ?Form $_set_add_item_form = null;

	/**
	 * @var Product_SetItem[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Product_SetItem::class,
	)]
	protected array $set_items = [];
	
	public function getSetDiscountType( Pricelist $pricelist ): string
	{
		return Product_Price::get( $pricelist, $this->getId() )->getSetDiscountType();
	}
	
	public function setSetDiscountType( Pricelist $pricelist, string $set_discount_type ): void
	{
		Product_Price::get( $pricelist, $this->getId() )->setSetDiscountType( $set_discount_type );
	}
	
	public function getSetDiscountValue( Pricelist $pricelist ): float
	{
		return Product_Price::get( $pricelist, $this->getId() )->getSetDiscountValue();
	}
	
	public function setSetDiscountValue( Pricelist $pricelist, float $set_discount_value ): void
	{
		Product_Price::get( $pricelist, $this->getId() )->setSetDiscountValue( $set_discount_value );
	}
	
	
	/**
	 * @return Product_SetItem[]
	 */
	public function getSetItems(): iterable
	{
		/**
		 * @var Product $this
		 */
		return $this->set_items;
	}
	

	public function addSetItem( int $item_product_id ) : Product_SetItem
	{
		if(isset( $this->set_items[$item_product_id])) {
			return $this->set_items[$item_product_id];
		}

		$product_type = static::getProductType( $item_product_id );
		
		if(!$product_type) {
			throw new Exception('Unknown product '.$item_product_id);
		}

		if(
			$product_type == static::PRODUCT_TYPE_VARIANT_MASTER ||
			$product_type == static::PRODUCT_TYPE_SET
		) {
			throw new Exception('Product '.$item_product_id.' can\'t be added as a set item - unsupported product type');
		}


		$set_item =  new Product_SetItem();
		$set_item->setProductId( $this->id );
		$set_item->setItemProductId( $item_product_id );
		$set_item->setCount( 1 );
		$set_item->setSortOrder( count($this->set_items)+1 );

		$this->set_items[$item_product_id] = $set_item;
		
		$set_item->save();
		$this->actualizeSet();

		return $set_item;
	}

	public function removeSetItem( int $product_id ) : void
	{
		if(isset($this->set_items[$product_id])) {
			$this->set_items[$product_id]->delete();
			unset($this->set_items[$product_id]);
			$this->actualizeSet();
		}
	}
	


	public function actualizeSet() : void
	{
		if($this->type!=static::PRODUCT_TYPE_SET) {
			return;
		}
		
		Product_Parameter_Value::syncSetItemsParameters(
			$this->id,
			array_keys($this->set_items)
		);
		
		foreach(Pricelists::getList() as $pricelist) {
			Product_Price::get( $pricelist, $this->getId() )->actualizeSet();
		}
		
		foreach(Availabilities::getList() as $availability) {
			Product_Availability::get( $availability, $this->getId() )->actualizeSet();
		}
	}
	
	

	public function actualizeSetItem() : void
	{
		$sets = Product_SetItem::dataFetchCol(select:['product_id'], where: ['item_product_id'=>$this->id]);
		
		foreach($sets as $set_id) {
			$set_product = static::load(['id'=>$set_id]);
			$set_product?->actualizeSet();
		}
	}
	
	
	
	
	public function getCalculatedSetPrice( Pricelist $pricelist ) : float|int
	{
		return Product_Price::get( $pricelist, $this->getId() )->getCalculatedSetPrice();
	}
	
	
	protected function _setupForm_set( Form $form ) : void
	{
		if(!$this->isSet()) {
			return;
		}
		$form->removeField('internal_name_of_variant');
		$form->addField( $this->generateKindOfProductField() );
		
		foreach( EShops::getList() as $eshop ) {
			$form->removeField( '/eshop_data/'.$eshop->getKey().'/variant_name' );
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
			
			foreach(Pricelists::getList() as $pricelist) {
				$discount_type = new Form_Field_Select(
					name: '/set_price/'.$pricelist->getCode().'/discount_type',
					label: 'Discount type:'
				);
				$discount_type->setDefaultValue( $this->getSetDiscountType($pricelist) );
				$discount_type->setSelectOptions([
					Product::SET_DISCOUNT_NONE     => Tr::_('None'),
					Product::SET_DISCOUNT_PERCENT  => Tr::_('Percent'),
					Product::SET_DISCOUNT_NOMINAL  => Tr::_('Nominal')
				]);
				$discount_type->setErrorMessages([
					Form_Field_Input::ERROR_CODE_INVALID_VALUE => 'Invalid value'
				]);
				$discount_type->setFieldValueCatcher( function() use ($discount_type, $pricelist) {
					$this->setSetDiscountType( $pricelist, $discount_type->getValue() );
				} );
				$this->_set_setup_form->addField( $discount_type );
				
				
				
				
				$discount_value = new Form_Field_Float(
					name: '/set_price/'.$pricelist->getCode().'/discount_value',
					label: 'Discount value:'
				);
				$discount_value->setDefaultValue(  $this->getSetDiscountValue($pricelist)  );
				$discount_value->setFieldValueCatcher( function() use ( $discount_value, $pricelist) {
					$this->setSetDiscountValue( $pricelist, $discount_value->getValue() );
				} );
				$this->_set_setup_form->addField( $discount_value );
				
			}
			
			
			
			
			if(
				!$this->isEditable()
			) {
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
		
		$this->save();
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
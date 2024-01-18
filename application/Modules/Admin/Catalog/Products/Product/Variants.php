<?php
namespace JetApplicationModule\Admin\Catalog\Products;


use Jet\Form;
use Jet\Form_Field_Int;
use JetApplication\Shops;

trait Product_Variants
{
	protected ?Form $_variant_setup_form = null;
	
	protected ?Form $_variant_add_form = null;
	
	protected ?Form $_update_variants_form = null;
	
	
	public function createNewVariantInstance() : static
	{
		/**
		 * @var Product $variant
		 */
		$variant = new static();
		
		$variant->setType( Product::PRODUCT_TYPE_VARIANT );
		$variant->is_active = true;
		$variant->setKindId( $this->getKindId() );
		$variant->setVariantMasterProductId( $this->getId() );
		$variant->setInternalName( $this->getInternalName() );
		$variant->setInternalCode( $this->getInternalCode() );
		
		return $variant;
	}
	
	protected function _setupForm_variantMaster( Form $form ) : void
	{
		if(!$this->isVariantMaster()) {
			return;
		}
		$form->removeField('internal_name_of_variant');
		
		foreach( Shops::getList() as $shop ) {
			$form->removeField('/shop_data/'.$shop->getKey().'/variant_name');

			$form->field('/shop_data/'.$shop->getKey().'/standard_price')->setIsReadonly( true );
			$form->field('/shop_data/'.$shop->getKey().'/price')->setIsReadonly( true );
			$form->field('/shop_data/'.$shop->getKey().'/in_stock_qty')->setIsReadonly( true );
			$form->field('/shop_data/'.$shop->getKey().'/length_of_delivery')->setIsReadonly( true );
			$form->field('/shop_data/'.$shop->getKey().'/available_from')->setIsReadonly( true );
		}
	}
	
	
	protected function _setupForm_variant( Form $form ) : void
	{
		if($this->type!=static::PRODUCT_TYPE_VARIANT) {
			return;
		}
		
		$form->setIsReadonly();
	}
	
	
	public function getAddVariantForm() : Form
	{
		
		if(!$this->_variant_add_form) {
			$this->_variant_add_form = $this->createForm('variant_add_form', [
				'ean',
				'internal_code',
				'internal_name_of_variant',
				'/shop_data/*/variant_name',
				'/shop_data/*/standard_price',
				'/shop_data/*/price',
				'/shop_data/*/in_stock_qty',
				'/shop_data/*/length_of_delivery',
				'/shop_data/*/available_from',
			]);
			
			
		}
		
		return $this->_variant_add_form;
	}
	
	public function catchAddVariantForm( Product $new_variant ) : bool
	{
		$edit_form = $new_variant->getAddVariantForm();
		if( $edit_form->catch() ) {
			$this->addVariant( $new_variant );
			
			return true;
		}
		
		return false;
	}
	
	public function getUpdateVariantsForm() : Form
	{
		if(!$this->_update_variants_form) {
			$fields = [];
			
			foreach($this->getVariants() as $variant) {
				/**
				 * @var Product $variant
				 */
				$_form = $variant->createForm('', [
					'ean',
					'internal_code',
					'internal_name_of_variant',
					'/shop_data/*/variant_name',
					'/shop_data/*/standard_price',
					'/shop_data/*/price',
					'/shop_data/*/in_stock_qty',
					'/shop_data/*/length_of_delivery',
					'/shop_data/*/available_from',
				]);
				
				
				foreach( Shops::getList() as $shop ) {
					$_form->field('/shop_data/'.$shop->getKey().'/standard_price')->setIsReadonly( true );
					$_form->field('/shop_data/'.$shop->getKey().'/price')->setIsReadonly( true );
				}
				
				
				foreach($_form->getFields() as $field) {
					$field_name = $field->getName();
					if($field_name[0]=='/') {
						$field_name = substr($field_name, 1);
					}
					
					$field_name = '/'.$variant->getId().'/'.$field_name;
					
					$field->setName( $field_name );
					
					$fields[] = $field;
				}
				
				$priority_field = new Form_Field_Int('/'.$variant->getId().'/variant_priority', 'Priority:');
				$priority_field->setDefaultValue( $variant->getVariantPriority() );
				$priority_field->setFieldValueCatcher( function( $value ) use ($variant) {
					$variant->setVariantPriority( (int)$value );
				} );
				$fields[] = $priority_field;
				
				
				$variant_control_properties = $this->getKindOfProduct()?->getVariantSelectorProperties() ? : [];
				
				foreach($variant_control_properties as $property) {
					
					$property->assocToProduct( $variant->getId() );
					
					foreach( $property->getValueEditForm()->getFields() as $field ) {
						$field->setName('/'.$variant->getId().'/'.$property->getId().'/'.$field->getName());
						$fields[] = $field;
					}
				}
				
			}
			
			$this->_update_variants_form = new Form('update_variants_form', $fields);
			if(!$this->isEditable()) {
				$this->_update_variants_form->setIsReadonly();
			}
		}
		
		return $this->_update_variants_form;
	}
	
	
	public function catchUpdateVariantsForm(): bool
	{
		$edit_form = $this->getUpdateVariantsForm();
		if( $edit_form->catch() ) {
			foreach($this->getVariants() as $variant) {
				$variant->save();
			}
			
			$this->actualizeVariantMaster();
			
			return true;
		}
		
		return false;
	}
	
}
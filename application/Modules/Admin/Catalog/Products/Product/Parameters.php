<?php
namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\Form;
use JetApplication\KindOfProduct;
use JetApplication\Property;


trait Product_Parameters  {
	
	protected ?Form $_parameters_edit_form = null;
	
	public function getKindOfProduct() : ?KindOfProduct
	{
		return KindOfProduct::load($this->getKindId());
	}
	
	public function getParametersEditForm() : Form
	{
		if(!$this->_parameters_edit_form) {
			
			$fields = [];
			$disable_properties = [];
			$property_ids = [];
			
			if($this->isSet()) {
				foreach($this->getSetItems() as $set_item) {
					/**
					 * @var Product $set_item_product
					 */
					$set_item_product = static::get( $set_item->getItemProductId() );
					if(!$set_item_product) {
						continue;
					}
					
					foreach($set_item_product->getParametersEditForm()->getFields() as $field) {
						$field->setIsReadonly( true );
						$field->setName('/set_item/'.$set_item->getItemProductId().$field->getName());
						
						$fields[] = $field;
					}
					
				}
				
				
			} else {
				$kind = $this->getKindOfProduct();
				if($kind) {
					$property_ids = $kind->getPropertyIds();
					
					if($this->isVariantMaster()) {
						$disable_properties = array_keys($kind->getVariantSelectorPropertyIds());
					}
				}
				
				if(!$property_ids) {
					$properties = [];
				} else {
					$properties = Property::getProperties($property_ids);
				}
				
				
				
				foreach($properties as $property) {
					
					$property->assocToProduct( $this->id );
					
					$property_id = $property->getId();
					
					foreach( $property->getValueEditForm()->getFields() as $field ) {
						$field->setName('/'.$property_id.'/'.$field->getName());
						
						if( in_array($property_id, $disable_properties) ) {
							$field->setIsReadonly(true);
						}
						
						$fields[] = $field;
					}
				}
				
			}
			
			
			
			$form = new Form('parameters_edit_form', $fields);
			$form->setDoNotTranslateTexts(true);
			
			if(
				$this->isSet() ||
				$this->isVariant()
			) {
				$form->setIsReadonly();
			}
			
			$this->_parameters_edit_form = $form;
		}
		
		return $this->_parameters_edit_form;
	}
	
	public function catchParametersEditForm() : bool
	{
		$edit_form = $this->getParametersEditForm();
		
		if($edit_form->catch()) {
			
			$this->save();
			
			return true;
		}
		
		return false;
		
	}
	
}
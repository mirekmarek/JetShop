<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Products;


use Jet\Form;
use Jet\Form_Field_MultiSelect;
use Jet\Http_Headers;
use Jet\Tr;
use Jet\UI_messages;
use Jet\Form_Field_Hidden;
use JetApplication\Accessories_Group;
use JetApplication\Product;


trait Controller_Main_Edit_Accessories
{
	
	public function edit_accessories_Action() : void
	{
		$this->setBreadcrumbNavigation( Tr::_('Accessories') );
		
		$this->view->setVar('item', $this->current_item);
		
		/**
		 * @var Product $product
		 */
		$product = $this->current_item;
		
		
		$editable = $product->isEditable();
		
		
		if(
			$product->getType()==Product::PRODUCT_TYPE_VARIANT
		) {
			$editable = false;
		}
		
		$groups = new Form_Field_MultiSelect('groups', 'Groups:');
		$groups->setSelectOptions(Accessories_Group::getScope());
		$groups->setDefaultValue( $product->getAccessoriesGroupIds() );
		$groups->setFieldValueCatcher( function( $value ) use ($product) {
			$product->setAccessoriesGroupIds( $value );
		} );
		
		$accessory = new Form_Field_Hidden('accessory', '');
		$accessory->setDefaultValue( implode(',', $product->getDirectAccessoriesIds() ) );
		$accessory->setFieldValueCatcher(function($value) use ($product) {
			$product->setAccessoriesIds( $value ? explode( ',', $value ) : [] );
		});
		
		$form = new Form('groups_edit_form', [$groups, $accessory]);
		if($editable) {
			if($form->catch()) {
				UI_messages::success(
					Tr::_( 'Accessories setting has been updated' )
				);
				
				Http_Headers::reload();
			}
		} else {
			$form->setIsReadonly();
		}
		
		
		$this->view->setVar('editable', $editable );
		$this->view->setVar('product', $product);
		$this->view->setVar('form', $form);
		
		
		
		$this->output( 'edit/accessories' );
		
	}
}
<?php
namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\Form;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Product_Box;

/**
 *
 */
trait Controller_Main_Edit_Boxes
{
	
	public function edit_boxes_Action() : void
	{
		$this->setBreadcrumbNavigation( Tr::_('Boxes') );
		
		$this->view->setVar('item', $this->current_item);
		
		/**
		 * @var Product $product
		 */
		$product = $this->current_item;
		
		
		$editable = $product->isEditable();
		
		
		if(
			$product->getType()==Product::PRODUCT_TYPE_VARIANT ||
			$product->getType()==Product::PRODUCT_TYPE_SET
		) {
			$editable = false;
		}
		$boxes = $product->getBoxes();
		
		$this->view->setVar('editable', $editable );
		$this->view->setVar('product', $product);
		$this->view->setVar('boxes', $boxes);
		
		if($editable) {
			if( ($delete=Http_Request::GET()->getInt('delete_box')) ) {
				$product->deleteBox( $delete );
				Http_Headers::reload( unset_GET_params: ['delete_box'] );
			}
			
			
			$new_box = new Product_Box();
			$new_box->setProductId( $product->getId() );
			$add_form = $new_box->createForm('new_box');
			$add_form->setAction( Http_Request::currentURI() );
			
			if($add_form->catch()) {
				$product->addBox( $new_box );
				Http_Headers::reload();
			}
			
			$this->view->setVar('add_form', $add_form);
			
			$edit_form = new Form('edit_form', []);
			
			foreach($product->getBoxes() as $box) {
				$box_form = $box->createForm('');
				foreach($box_form->getFields() as $field) {
					$field->setName('/'.$box->getId().'/'.$field->getName());
					$edit_form->addField( $field );
				}
			}
			
			if($edit_form->catch()) {
				foreach($product->getBoxes() as $box) {
					$box->save();
				}
				
				Http_Headers::reload();
			}
			
			
			$this->view->setVar('edit_form', $edit_form);
		}
		
		
		$this->output( 'edit/boxes' );
		
	}
}
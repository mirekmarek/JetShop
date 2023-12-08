<?php
namespace JetApplicationModule\Admin\Catalog\Products;


/**
 *
 */
trait Controller_Main_Edit_Variants
{
	
	
	public function edit_variants_Action() : void
	{
		$this->_setBreadcrumbNavigation();
		
		$product = static::getCurrentProduct();
		
		$updated = false;
		$sync = false;
		
		//TODO: it's shit ... revision needed
		
		$new_variant = new Product();
		
		if( $product->catchAddVariantForm( $new_variant ) ) {
			$updated = true;
		}
		
		if( $product->catchUpdateVariantsForm() ) {
			$updated = true;
		}
		
		/*
		
		
		
		
		if($updated) {
			$product->save();
			if($sync) {
				$product->syncVariants();
			}
			Category::syncCategories();
			
			Logger::success(
				'product_updated',
				'Product '.$product->getAdminTitle().' ('.$product->getId().') updated',
				$product->getId(),
				$product->getAdminTitle(),
				$product
			);
			
			UI_messages::success(
				Tr::_( 'Product <b>%NAME%</b> has been updated', [ 'NAME' => $product->getAdminTitle() ] )
			);
			
			Http_Headers::reload();
		}
		*/
		
		$this->view->setVar('new_variant', $new_variant);
		
		
		$this->output( 'edit/variants' );
	}
	
}
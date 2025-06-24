<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Products;



use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Category;
use JetApplication\Product;


trait Controller_Main_Edit_Categories
{
	
	public function edit_categories_Action() : void
	{
		$this->setBreadcrumbNavigation( Tr::_('Categories') );
		
		/**
		 * @var Product $product
		 */
		$product = $this->current_item;
		
		if($product->isVariant()) {
			$product = $product->getVariantMasterProduct();
		}
		
		if(Main::getCurrentUserCanEdit()) {
			$GET = Http_Request::GET();
			
			if( ($add_category=$GET->getInt('add_category')) ) {
				$category = Category::get($add_category);
				if($category) {
					$category->addProduct( $product->getId() );
					$category->actualizeCategoryBranchProductAssoc();
				}
				
				Http_Headers::reload(unset_GET_params: ['add_category']);
			}
			
			if( ($remove_category=$GET->getInt('remove_category')) ) {
				$category = Category::get($remove_category);
				if($category) {
					$category->removeProduct( $product->getId() );
					$category->actualizeCategoryBranchProductAssoc();
				}
				
				Http_Headers::reload(unset_GET_params: ['remove_category']);
			}
		}
		
		$this->view->setVar('item', $this->current_item);
		$this->view->setVar('product', $product);
		
		$this->output( 'edit/categories' );
	}
	
}
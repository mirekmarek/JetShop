<?php
namespace JetShopModule\Admin\Catalog\Products;

use Jet\Logger;
use Jet\UI_messages;

use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;

use JetShop\Category;
use JetShop\Product;


/**
 *
 */
trait Controller_Main_Edit_Categories
{
	
	public function edit_categories_Action() : void
	{
		$this->_setBreadcrumbNavigation();
		
		$product = static::getCurrentProduct();
		
		$allowed = true;
		if(
			$product->getType()==Product::PRODUCT_TYPE_VARIANT &&
			($master=Product::get($product->getVariantMasterProductId())) &&
			$master->isVariantSyncCategories()
		) {
			$allowed = false;
		}
		
		if($allowed) {
			$POST = Http_Request::POST();
			
			$updated = false;
			switch($POST->getString('action')) {
				case 'add_category':
					if($product->addCategory( $POST->getInt('category_id') )) {
						$updated = true;
					}
					break;
				case 'remove_category':
					if($product->removeCategory( $POST->getInt('category_id') )) {
						$updated = true;
					}
					break;
			}
			
			if($updated) {
				$product->save();
				$product->syncVariants();
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
		} else {
			$product->getEditForm()->setIsReadonly();
		}
		
		$this->output( 'edit/categories' );
	}
	
}
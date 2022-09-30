<?php
namespace JetShopModule\Admin\Catalog\Products;


use Jet\AJAX;
use Jet\Http_Request;
use JetShop\Application_Admin;
use JetShop\Shops;

/**
 *
 */
trait Controller_Main_Edit_Images
{
	
	public function edit_images_Action() : void
	{
		$this->_setBreadcrumbNavigation();
		
		$GET = Http_Request::GET();
		
		if($GET->exists('action')) {
			$product = static::getCurrentProduct();
			$shop = Shops::get( $GET->getString('shop_key') );
			
			$shop_data = $product->getShopData($shop);
			$this->view->setVar('shop', $shop );
			
			$updated = false;
			switch($GET->getString('action')) {
				case 'upload':
					Application_Admin::handleUploadTooLarge();
					
					$shop_data->uploadImages();
					$updated = true;
					break;
				case 'delete':
					$shop_data->deleteImages( explode(',', $GET->getString('images')) );
					$updated = true;
					break;
				case 'save_sort':
					$shop_data->sortImages( explode(',', $GET->getString('images')) );
					$updated = true;
					break;
			}
			
			if($updated) {
				$product->save();
				
				AJAX::commonResponse(
					[
						'result' => 'ok',
						'snippets' => [
							'images_'.$shop->getKey() => $this->view->render('edit/images/list')
						]
					
					]
				);
				
			}
		}
		
		
		
		$this->output( 'edit/images' );
		
	}
}
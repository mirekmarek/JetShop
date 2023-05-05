<?php
namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\AJAX;
use Jet\Http_Request;
use JetApplication\Application_Admin;

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
			$suffix = $GET->getString('suffix');
			
			$this->view->setVar('product', $product);;
			$this->view->setVar('suffix', $suffix);
			
			$updated = false;
			switch($GET->getString('action')) {
				case 'upload':
					Application_Admin::handleUploadTooLarge();
					
					$product->uploadImages();
					$updated = true;
					break;
				case 'delete':
					$product->deleteImages( explode(',', $GET->getString('images')) );
					$updated = true;
					break;
				case 'save_sort':
					$product->sortImages( explode(',', $GET->getString('images')) );
					$updated = true;
					break;
			}
			
			if($updated) {
				$product->save();
				
				AJAX::commonResponse(
					[
						'result' => 'ok',
						'snippets' => [
							'images_list'.$suffix => $this->view->render('edit/images/list')
						]
					
					]
				);
				
			}
		}
		
		
		
		$this->output( 'edit/images' );
		
	}
}
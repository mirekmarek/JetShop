<?php
namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\Logger;
use Jet\UI_messages;

use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;

use JetApplication\Category;

/**
 *
 */
trait Controller_Main_Edit_Main
{
	
	public function edit_main_Action() : void
	{
		$product = static::getCurrentProduct();
		$this->_setBreadcrumbNavigation();
		$product->handleActivation();
		
		$GET = Http_Request::GET();
		
		if($GET->exists('action')) {
			$action = $GET->getString('action');
			if($action=='change_kind_of_product') {
				$product->setKindId( Http_Request::POST()->getInt('kind_of_product_id') );
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
			}
			
			Http_Headers::reload(unset_GET_params: ['action']);
		}
		
		
		if( $product->catchEditForm() ) {
			
			$product->save();
			$product->syncVariants();
			
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
		
		$this->view->setVar('listing', $this->getListing());
		
		$this->output( 'edit/main' );
	}
}
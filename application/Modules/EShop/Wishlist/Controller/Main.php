<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\EShop\Wishlist;

use Jet\AJAX;
use Jet\Http_Request;
use Jet\MVC_Controller_Default;
use JetApplication\Product_EShopData;

/**
 *
 */
class Controller_Main extends MVC_Controller_Default
{

	/**
	 *
	 */
	public function default_Action() : void
	{
		$GET = Http_Request::GET();
		
		/**
		 * @var Main $module
		 */
		$module = $this->module;
		
		if( ($id=$GET->getInt('select')) ) {
			$product = Product_EShopData::get($id);
			if($product) {
				$module->selectProduct( $id );
				
				$this->view->setVar('id', $product->getId());
				
				AJAX::operationResponse(
					true,snippets: [
						'wishlist_btn_'.$id => $this->view->render('button/selected'),
						'wishlist_icon' => $module->renderIcon()
					]
				);
			}
		}
		
		if( ($id=$GET->getInt('unselect')) ) {
			$product = Product_EShopData::get($id);
			if($product) {
				$module->unselectProduct( $id );
				
				$this->view->setVar('id', $product->getId());
				
				AJAX::operationResponse(
					true,snippets: [
					'wishlist_btn_'.$id => $this->view->render('button/select'),
					'wishlist_icon' => $module->renderIcon()
				]
				);
			}
		}
		
		$product_ids = $module->getProductIds();
		if(!$product_ids) {
			return;
		}
		
		$products = Product_EShopData::getActiveList( $product_ids );
		if(!$products) {
			return;
		}
		
		$this->view->setVar('products', $products);
		
		
		$this->output('default');
	}
}
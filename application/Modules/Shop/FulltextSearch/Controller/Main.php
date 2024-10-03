<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Shop\FulltextSearch;

use Jet\AJAX;
use Jet\Application;
use Jet\Http_Request;
use Jet\MVC_Controller_Default;
use JetApplication\Category;
use JetApplication\Category_ShopData;
use JetApplication\Pricelists;
use JetApplication\Product;
use JetApplication\Product_ShopData;
use JetApplication\Shop_Managers;
use JetApplication\Shops;

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
		$q = $GET->getString('q');
		$products = null;
		$categories = [];
		
		if(strlen($q)>3) {
			$product_ids = Index::search(
				shop: Shops::getCurrent(),
				entity_type: Product::getEntityType(),
				search_string: $q
			);
			if($product_ids) {
				$products = Shop_Managers::ProductListing();
				$products->init(
					product_ids: $product_ids,
					category_id: 0,
					category_name: 'search_result',
					optional_URL_parameter: 'q='.rawurlencode($GET->getRaw('q'))
				);
			}
			
			$category_ids = Index::search(
				shop: Shops::getCurrent(),
				entity_type: Category::getEntityType(),
				search_string: $q
			);
			
			foreach($category_ids as $c_id) {
				$category = Category_ShopData::get( $c_id );
				if(
					!$category->isActive() ||
					!$category->getBranchProductsCount()
				) {
					continue;
				}
				
				$categories[] = $category;
			}
		}
		
		
		$this->view->setVar('q', $q);
		$this->view->setVar('products', $products);
		$this->view->setVar('categories', $categories);
		
		$this->output('default');
	}
	
	public function whisper_Action() : void
	{
		$GET = Http_Request::GET();
		$q = $GET->getString('q');
		
		if(strlen($q)<=3) {
			Application::end();
		}
		
		$products = [];
		$categories = [];
		
		
		$product_ids = Index::search(
			shop: Shops::getCurrent(),
			entity_type: Product::getEntityType(),
			search_string: $q
		);
		if($product_ids) {
			$products = Product_ShopData::getActiveList( $product_ids );
			
			uasort( $products, function( Product_ShopData $a, Product_ShopData $b ) {
				$pricelist = Pricelists::getCurrent();
				return $a->getPrice($pricelist) <=> $b->getPrice($pricelist);
			} );
			
			$products = array_splice($products, 0, 20);
		}
		
		$category_ids = Index::search(
			shop: Shops::getCurrent(),
			entity_type: Category::getEntityType(),
			search_string: $q
		);
		
		foreach($category_ids as $c_id) {
			$category = Category_ShopData::get( $c_id );
			if(
				!$category->isActive() ||
				!$category->getBranchProductsCount()
			) {
				continue;
			}
			
			$categories[] = $category;
		}
		
		$this->view->setVar('products', $products);
		$this->view->setVar('categories', $categories);
		
		AJAX::snippetResponse(
			$this->view->render('whisper')
		);
		
	}
	
}
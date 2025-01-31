<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\EShop\FulltextSearch;


use Jet\AJAX;
use Jet\Application;
use Jet\Http_Request;
use Jet\MVC_Controller_Default;
use JetApplication\Category;
use JetApplication\Category_EShopData;
use JetApplication\Pricelists;
use JetApplication\Product;
use JetApplication\Product_EShopData;
use JetApplication\EShop_Managers;
use JetApplication\EShops;


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
				eshop: EShops::getCurrent(),
				entity_type: Product::getEntityType(),
				search_string: $q
			);
			if($product_ids) {
				$products = EShop_Managers::ProductListing();
				$products->init(
					product_ids: $product_ids,
					category_id: 0,
					category_name: 'search_result',
					optional_URL_parameter: 'q='.rawurlencode($GET->getRaw('q'))
				);
			}
			
			$category_ids = Index::search(
				eshop: EShops::getCurrent(),
				entity_type: Category::getEntityType(),
				search_string: $q
			);
			
			foreach($category_ids as $c_id) {
				$category = Category_EShopData::get( $c_id );
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
			eshop: EShops::getCurrent(),
			entity_type: Product::getEntityType(),
			search_string: $q
		);
		if($product_ids) {
			$products = Product_EShopData::getActiveList( $product_ids );
			
			uasort( $products, function( Product_EShopData $a, Product_EShopData $b ) {
				$pricelist = Pricelists::getCurrent();
				return $a->getPrice($pricelist) <=> $b->getPrice($pricelist);
			} );
			
			$products = array_splice($products, 0, 20);
		}
		
		$category_ids = Index::search(
			eshop: EShops::getCurrent(),
			entity_type: Category::getEntityType(),
			search_string: $q
		);
		
		foreach($category_ids as $c_id) {
			$category = Category_EShopData::get( $c_id );
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
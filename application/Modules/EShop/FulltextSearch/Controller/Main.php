<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\FulltextSearch;


use Jet\AJAX;
use Jet\Application;
use Jet\Http_Request;
use Jet\MVC_Controller_Default;
use JetApplication\Category;
use JetApplication\Category_EShopData;
use JetApplication\Application_Service_EShop_ProductListing;
use JetApplication\Content_Article;
use JetApplication\Content_Article_EShopData;
use JetApplication\Pricelists;
use JetApplication\Product;
use JetApplication\Product_EShopData;
use JetApplication\Application_Service_EShop;
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
		
		$result_ids = [];
		
		$products = null;
		$articles = [];
		$categories = [];
		
		if(strlen($q)>3) {
			
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
				
				$categories[$c_id] = $category;
			}
			if($categories) {
				$result_ids['categories'] = array_keys($categories);
			}
			
			
			$article_ids = Index::search(
				eshop: EShops::getCurrent(),
				entity_type: Content_Article::getEntityType(),
				search_string: $q,
			);
			$article_ids = array_unique($article_ids);
			
			if($article_ids) {
				$result_ids['articles'] = $article_ids;
				foreach( Content_Article_EShopData::getActiveList($article_ids) as $article ) {
					$articles[$article->getId()] = $article;
				}
			}
			
			
			
			$product_ids = Index::search(
				eshop: EShops::getCurrent(),
				entity_type: Product::getEntityType(),
				search_string: $q
			);
			if($product_ids) {
				$result_ids['products'] = $product_ids;
				
				$products = Application_Service_EShop::ProductListing();
				$products->init( $product_ids );
				$products->setOptionalURLParameter('q='.rawurlencode($GET->getRaw('q')) );
				$products->setAjaxEventHandler( function(Application_Service_EShop_ProductListing $listing) use ($q, $result_ids) {
					Application_Service_EShop::AnalyticsManager()?->search( $q, $result_ids, $listing->getListing() );
				} );
				
				$products->handle();
				
			}
			
		}
		
		
		
		
		$this->view->setVar('q', $q);
		$this->view->setVar('products', $products);
		$this->view->setVar('articles', $articles);
		$this->view->setVar('categories', $categories);
		$this->view->setVar('result_ids', $result_ids);
		
		$this->output('default');
	}
	
	public function whisper_Action() : void
	{
		$GET = Http_Request::GET();
		$q = $GET->getString('q');
		
		$result_ids = [];
		
		if(strlen($q)<=3) {
			Application::end();
		}
		
		$products = [];
		$categories = [];
		$articles = [];
		
		
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
			
			$result_ids['products'] = array_keys($products);
		}
		
		
		
		$article_ids = Index::search(
			eshop: EShops::getCurrent(),
			entity_type: Content_Article::getEntityType(),
			search_string: $q,
		);
		$article_ids = array_unique($article_ids);
		shuffle($article_ids);
		$article_ids = array_slice($article_ids, 0, 5);
		
		if($article_ids) {
			$result_ids['articles'] = $article_ids;
			foreach( Content_Article_EShopData::getActiveList($article_ids) as $article ) {
				$articles[$article->getId()] = $article;
			}
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
			
			$categories[$c_id] = $category;
		}
		
		if($categories) {
			$result_ids['categories'] = array_keys($categories);
		}
		
		$this->view->setVar('q', $q);
		$this->view->setVar('products', $products);
		$this->view->setVar('articles', $articles);
		$this->view->setVar('categories', $categories);
		$this->view->setVar('result_ids', $result_ids);
		
		AJAX::snippetResponse(
			$this->view->render('whisper')
		);
		
	}
	
}
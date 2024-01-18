<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Shop\Catalog;

use Jet\Application;
use Jet\Application_Module;
use Jet\Factory_MVC;
use Jet\Http_Request;
use JetApplication\ProductListing;

/**
 *
 */
class Main extends Application_Module
{

	public function getProductListing( array $product_ids, ?int $category_id=null ) : ProductListing
	{
		$GET = Http_Request::GET();
		
		$listing = new ProductListing( $product_ids );
		if($category_id) {
			$listing->setCategoryId( $category_id );
		}
		
		$listing->initPaginator(
			current_page_no: Http_Request::GET()->getInt('p'),
			items_per_page: 40,
			URL_creator: function( int $page_no ) : string {
				return Http_Request::currentURI(set_GET_params: ['p'=>$page_no]);
			} );
		
		if($GET->exists('in-stock')) {
			$listing->getFilter()->getBasicFilter()->setInStock(true);
		}
		if($GET->exists('has-discount')) {
			$listing->getFilter()->getBasicFilter()->setHasDiscount(true);
		}
		
		$listing->setupPriceFilter(
			$GET->getFloat('p_min'),
			$GET->getFloat('p_max')
		);
		
		if(is_array($property_options = $GET->getRaw('o'))) {
			foreach($property_options as $property_id=>$options) {
				$property_options[$property_id] = explode(',', $options);
			}
			$listing->setProductOptionsFilter( $property_options );
		}
		
		if(is_array($numbers = $GET->getRaw('n'))) {
			$price_filter = [];
			foreach($numbers as $property_id=>$rules) {
				$min = $rules['min']??null;
				$max = $rules['max']??null;
				
				if($min!==null) {
					$min = (float)$min;
				}
				if($max!==null) {
					$max = (float)$max;
				}
				
				if($min===null && $max===null) {
					continue;
				}
				$price_filter[ $property_id ] = [
					'min' => $min,
					'max' => $max
				];
			}
			
			if($price_filter) {
				$listing->setNumbersFilter( $price_filter );
			}

		}
		
		if(($bools = $GET->getRaw('by'))) {
			$listing->setBoolYesFilter( explode(',', $bools) );
		}
		
		if(($brands = $GET->getRaw('b'))) {
			$listing->setBrandFilter( explode(',', $brands) );
		}
		
		if(($sort=$GET->getString('sort', valid_values: array_keys($listing->getSorters())))) {
			$listing->selectSorter( $sort );
		}
		
		$listing->handle();
		
		if(isset(Http_Request::headers()['listing-ajax'])) {
			$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
			$view->setVar('listing', $listing);
			
			if(isset(Http_Request::headers()['listing-ajax-only-products'])) {
				echo $view->render( 'product_listing/list' );
			} else {
				echo $view->render( 'product_listing/listing' );
			}
			
			Application::end();
		}
		
		return $listing;
	}
}
<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\ProductListing;

use Closure;
use Jet\Application;
use Jet\Http_Request;
use JetApplication\Product_EShopData;
use JetApplication\ProductListing;
use JetApplication\EShop_Managers_ProductListing;
use JetApplication\EShop_ModuleUsingTemplate_Interface;
use JetApplication\EShop_ModuleUsingTemplate_Trait;

class Main extends EShop_Managers_ProductListing implements EShop_ModuleUsingTemplate_Interface
{
	use EShop_ModuleUsingTemplate_Trait;
	
	protected ProductListing $listing;
	protected string $optional_URL_parameter = '';
	protected int $category_id = 0;
	protected ?Closure $ajax_event_handler = null;
	
	public function init( array $product_ids ) : void
	{
		$this->listing = new ProductListing( $product_ids );
	}
	
	public function setOptionalURLParameter( ?string $optional_URL_parameter ): void
	{
		$this->optional_URL_parameter = $optional_URL_parameter;
	}
	
	public function setCategoryId( int $category_id ): void
	{
		$this->category_id = $category_id;
		$this->listing->setCategoryId( $category_id );
	}
	
	
	public function setAjaxEventHandler( Closure $ajax_event_handler ): void
	{
		$this->ajax_event_handler = $ajax_event_handler;
	}
	
	
	public function handle() : void
	{
		header('Access-Control-Allow-Headers:X-Requested-With, Content-Type, Authorization');
		header('Access-Control-Allow-Methods:GET, POST, PUT, DELETE, OPTIONS');
		header('Access-Control-Allow-Origin:*');
		
		$GET = Http_Request::GET();
		
		$this->listing->initPaginator(
			current_page_no: $GET->getInt('p'),
			items_per_page: 36,
			URL_creator: function( int $page_no ) : string {
				return Http_Request::currentURI(set_GET_params: ['p'=>$page_no]);
			} );
		
		if($GET->exists('in-stock')) {
			$this->listing->getFilter()->getBasicFilter()->setInStock(true);
		}
		if($GET->exists('has-discount')) {
			$this->listing->getFilter()->getBasicFilter()->setHasDiscount(true);
		}
		
		$this->listing->setupPriceFilter(
			$GET->getFloat('p_min'),
			$GET->getFloat('p_max')
		);
		
		if(is_array($property_options = $GET->getRaw('o'))) {
			foreach($property_options as $property_id=>$options) {
				$property_options[$property_id] = explode(',', $options);
			}
			$this->listing->setProductOptionsFilter( $property_options );
		}
		
		if(is_array($numbers = $GET->getRaw('n'))) {
			$numbers_filter = [];
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
				$numbers_filter[ $property_id ] = [
					'min' => $min,
					'max' => $max
				];
			}
			
			if($numbers_filter) {
				$this->listing->setNumbersFilter( $numbers_filter );
			}
			
		}
		
		if(($bools = $GET->getRaw('by'))) {
			$this->listing->setBoolYesFilter( explode(',', $bools) );
		}
		
		if(($brands = $GET->getRaw('b'))) {
			$this->listing->setBrandFilter( explode(',', $brands) );
		}
		
		if(($sort=$GET->getString('sort', valid_values: array_keys($this->listing->getSorters())))) {
			$this->listing->selectSorter( $sort );
		}
		
		$this->listing->handle();
		
		if(isset(Http_Request::headers()['Listing-Ajax'])) {
			$view = $this->getView();
			$view->setVar('listing', $this->listing);
			$view->setVar('optional_URL_parameter', $this->optional_URL_parameter);
			$view->setVar('c_id', $this->category_id);
			
			if(isset(Http_Request::headers()['Listing-Ajax-Only-Products'])) {
				echo $view->render( 'list' );
			} else {
				echo $view->render( 'listing' );
			}
			
			if($this->ajax_event_handler) {
				$handler = $this->ajax_event_handler;
				
				$handler( $this );
			}
			
			Application::end();
		}
		
	}
	
	
	public function getListing(): ProductListing
	{
		return $this->listing;
	}
	
	
	
	public function render() : string
	{
		$view = $this->getView();
		$view->setVar('listing', $this->listing);
		$view->setVar('c_id', $this->category_id);
		$view->setVar('optional_URL_parameter', $this->optional_URL_parameter);
		
		return $view->render( 'default' );
	}
	
	public function renderItem( Product_EShopData $item ) : string
	{
		$view = $this->getView();
		$view->setVar('product', $item);
		$view->setVar('c_id', $this->category_id);
		
		return $view->render( 'list/item' );
		
	}
}
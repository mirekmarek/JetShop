<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\EShopAnalyticsTester;

use Jet\Http_Request;
use Jet\MVC_Controller_Default;
use JetApplication\Application_Service_EShop;
use JetApplication\Category_EShopData;
use JetApplication\EShops;
use JetApplication\ProductListing;
use JetApplication\Signpost_EShopData;
use JetApplication\Order;

class Controller_Main extends MVC_Controller_Default
{

	public function default_Action() : void
	{
		$GET = Http_Request::GET();
		$eshops = array_keys( EShops::getList() );
		$eshop_key = $GET->getString('eshop', default_value: $eshops[0], valid_values: $eshops);
		$eshop = EShops::get( $eshop_key );
		
		$services = Application_Service_EShop::AnalyticsServices( $eshop );
		$services_list = array_keys( $services );
		$services_list[] = '';
		
		$service_id = $GET->getString('service', default_value: '', valid_values: $services_list);
		$service = $service_id ? $services[$service_id] : null;
		
		$service?->initTest( $eshop );
		
		$tests = [];
		if(
			$service &&
			$service->getEnabled() &&
			$service->getTestingAllowed()
		) {

			$tests[] = new class($service, $eshop) extends Test {
				protected string $title = 'Header';
				
				public function performTest(): string
				{
					return $this->service->header();
				}
			};
			
			$tests[] = new class($service, $eshop) extends Test {
				protected string $title = 'Document - start';
				
				public function performTest(): string
				{
					return $this->service->documentStart();
				}
			};
			
			$tests[] = new class($service, $eshop) extends Test {
				protected string $title = 'Document - end';
				
				public function performTest(): string
				{
					return $this->service->documentEnd();
				}
			};
			
			$tests[] = new class($service, $eshop) extends Test {
				protected string $title = 'View HomePage';
				
				public function performTest(): string
				{
					return $this->service->viewHomePage();
				}
			};
			
			$tests[] = new class($service, $eshop) extends Test {
				protected string $title = 'View Signpost';
				
				public function performTest(): string
				{
					$signposts = Signpost_EShopData::getAllActive( $this->eshop );
					shuffle( $signposts );
					$signposts= array_values( $signposts )[0];
					
					return $this->service->viewSignpost( $signposts );
				}
			};
			
			$tests[] = new class($service, $eshop) extends Test {
				protected string $title = 'View Category';
				
				public function performTest(): string
				{
					$categories = Category_EShopData::getAllActive( $this->eshop );
					shuffle( $categories );
					$category = array_values( $categories )[0];
					
					$product_listing = new ProductListing( $category->getProductIds(), $this->eshop );
					$product_listing->setCategoryId( $category->getId() );
					$product_listing->initPaginator(
						current_page_no: 1,
						items_per_page: 36,
						URL_creator: function( int $page_no ) : string {
							return '';
						} );
					
					$product_listing->handle();
					
					
					return $this->service->viewCategory( $category, $product_listing );
				}
			};
			
			
			$tests[] = new class($service, $eshop) extends Test {
				protected string $title = 'View Product';
				
				public function performTest(): string
				{
					return $this->service->viewProductDetail( $this->getRandomProduct() );
				}
			};
			
			$tests[] = new class($service, $eshop) extends Test {
				protected string $title = 'Purchase';
				
				public function performTest(): string
				{
					$order_ids = Order::dataFetchCol(
						select: ['id'],
						where: [
							$this->eshop->getWhere()
						],
						order_by: '-id',
						limit: 100
					);
					
					if(!$order_ids) {
						return 'No data - no orders';
					}
					
					shuffle( $order_ids );
					
					$order = Order::get( $order_ids[0] );
					
					return $this->service->purchase( $order );
				}
			};
			
			
			$tests[] = new class($service, $eshop) extends Test {
				protected string $title = 'Shopping Cart - add';
				
				public function performTest(): string
				{
					$cart = Application_Service_EShop::ShoppingCart( $this->eshop )->getCart();
					
					$new_item = $cart->addItem( $this->getRandomProduct(), 1 );
					$new_item = $cart->addItem( $this->getRandomProduct(), 1 );
					$new_item = $cart->addItem( $this->getRandomProduct(), 1 );
					
					return $this->service->addToCart( $new_item );
				}
			};
			
			$tests[] = new class($service, $eshop) extends Test {
				protected string $title = 'Shopping Cart - view';
				
				public function performTest(): string
				{
					$cart = Application_Service_EShop::ShoppingCart( $this->eshop )->getCart();
					
					return $this->service->viewCart( $cart );
				}
			};
			
			$tests[] = new class($service, $eshop) extends Test {
				protected string $title = 'Shopping Cart - remove';
				
				public function performTest(): string
				{
					$cart = Application_Service_EShop::ShoppingCart( $this->eshop )->getCart();
					$removed = null;
					foreach($cart->getItems() as $item){
						$removed = $cart->removeItem( $item->getProductId() );
						break;
					}
					
					if(!$removed) {
						return 'no data';
					}
					
					return $this->service->removeFromCart( $removed );
				}
			};
			
			/*
			$tests[] = new class($service, $eshop) extends Test {
				protected string $title = 'CashDesk - begin checkout';
				
				public function performTest(): string
				{
					$cash_desk = Application_Service_EShop::CashDesk( $this->eshop )->getCashDesk();
					
					return $this->service->beginCheckout( $cash_desk );
				}
			};
			
			$tests[] = new class($service, $eshop) extends Test {
				protected string $title = 'CashDesk - checkout in progress';
				
				public function performTest(): string
				{
					$cash_desk = Application_Service_EShop::CashDesk( $this->eshop )->getCashDesk();
					
					return $this->service->checkoutInProgress( $cash_desk );
				}
			};
			*/
			
			/*
			searchWhisperer( string $q, array $result_ids, ?ProductListing $product_listing=null ) : string;
			search( string $q, array $result_ids, ?ProductListing $product_listing=null ) : string;
			 */
			
		}
		
		
		$this->view->setVar( 'eshop', $eshop );
		$this->view->setVar( 'services', $services );
		$this->view->setVar( 'service', $service );
		$this->view->setVar( 'service_id', $service_id );
		$this->view->setVar( 'tests', $tests );
		
		
		
		$this->output( 'default' );
	}
}
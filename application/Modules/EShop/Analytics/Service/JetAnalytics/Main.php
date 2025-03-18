<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;


use JetApplication\Admin_EntityManager_EditTabProvider;
use JetApplication\Admin_EntityManager_EditTabProvider_EditTab;
use JetApplication\CashDesk;
use JetApplication\Category;
use JetApplication\Category_EShopData;
use JetApplication\Customer;
use JetApplication\EShopEntity_Basic;
use JetApplication\KindOfProduct;
use JetApplication\Order;
use JetApplication\Product;
use JetApplication\Product_EShopData;
use JetApplication\EShop_Analytics_Service;
use JetApplication\ProductListing;
use JetApplication\ShoppingCart;
use JetApplication\ShoppingCart_Item;
use JetApplication\Signpost;
use JetApplication\Signpost_EShopData;


class Main extends EShop_Analytics_Service implements Admin_EntityManager_EditTabProvider
{
	
	protected null|false|Session $session = null;
	
	
	public function init() : void
	{
	}
	
	protected function initSession() : void
	{
		if($this->session===null) {
			$this->session = Session::getCurrent();
		}
	}
	
	public function header(): string
	{
		$this->initSession();
		return '';
	}
	
	public function generateEvent( string $event, array $event_data=[] ) : string
	{
		return '';
	}
	
	
	public function documentStart(): string
	{
		$this->initSession();
		return '';
	}
	
	public function documentEnd(): string
	{
		$this->initSession();
		return '';
	}
	
	public function viewCategory( Category_EShopData $category ): string
	{
		$this->initSession();
		if($this->session) {
			$event = Event_CategoryView::create();
			$event->init( $category );
			$this->session->addEvent( $event );
		}
		
		return '';
	}
	
	public function viewSignpost( Signpost_EShopData $signpost ): string
	{
		$this->initSession();
		if($this->session) {
			$event = Event_SignpostView::create();
			$event->init( $signpost );
			$this->session->addEvent( $event );
		}
		
		return '';
	}
	
	
	public function viewProductsList( ProductListing $list, string $category_name='', string $category_id='' ) : string
	{
		$this->initSession();
		if($this->session) {
			$event = Event_ProductsListView::create();
			$event->init( $list, $category_name, $category_name );
			$this->session->addEvent( $event );
		}
		
		return '';
	}
	
	public function viewProductDetail( Product_EShopData $product ) : string
	{
		$this->initSession();
		if($this->session) {
			$event = Event_ProductDetailView::create();
			$event->init( $product );
			$this->session->addEvent( $event );
		}
		
		return '';
	}
	
	
	public function addToCart( ShoppingCart_Item $new_cart_item ) : string
	{
		$this->initSession();
		if($this->session) {
			$event = Event_AddToCart::create();
			$event->init( $new_cart_item );
			$this->session->addEvent( $event );
		}
		
		return '';
	}
	
	public function removeFromCart( ShoppingCart_Item $cart_item ) : string
	{
		$this->initSession();
		if($this->session) {
			$event = Event_RemoveFromCart::create();
			$event->init( $cart_item );
			$this->session->addEvent( $event );
		}
		
		return '';
	}
	
	public function viewCart( ShoppingCart $cart ) : string
	{
		$this->initSession();
		if($this->session) {
			$event = Event_CartView::create();
			$event->init( $cart );
			$this->session->addEvent( $event );
		}
		
		return '';
	}
	
	
	public function beginCheckout( CashDesk $cash_desk ) : string
	{
		$this->initSession();
		if($this->session) {
			$event = Event_CheckoutStarted::create();
			$event->init( $cash_desk->getOrder() );
			$this->session->addEvent( $event );
		}
		return '';
	}
	
	public function checkoutInProgress( CashDesk $cash_desk ) : string
	{
		$this->initSession();
		if($this->session) {
			$event = Event_CheckoutInProgress::create();
			$event->init( $cash_desk->getOrder() );
			$this->session->addEvent( $event );
		}
		return '';
	}

	
	public function purchase( Order $order ) : string
	{
		if($this->session) {
			$event = Event_Purchase::create();
			$event->init( $order );
			$this->session->addEvent( $event );
		}
		return '';
	}
	
	public function searchWhisperer( string $q, array $result_ids ) : string
	{
		$this->initSession();
		if($this->session) {
			$event = Event_SearchWhisperer::create();
			$event->init( $q, $result_ids );
			$this->session->addEvent( $event );
		}

		return '';
	}
	
	public function search( string $q, array $result_ids ) : string
	{
		$this->initSession();
		if($this->session) {
			$event = Event_Search::create();
			$event->init( $q, $result_ids );
			$this->session->addEvent( $event );
		}
		return '';
	}
	
	public function customEvent( string $evetnt, array $event_data = [] ): string
	{
		$this->initSession();
		if($this->session) {
			$event = Event_Custom::create();
			$event->init( $evetnt, $event_data );
			$this->session->addEvent( $event );
		}
		return '';
	}
	
	public function provideEditTabs( EShopEntity_Basic $item ): array
	{
		$res = [];
		
		switch($item::getEntityType()) {
			
			
			case Customer::getEntityType():
				$user_activity_tab = new Admin_EntityManager_EditTabProvider_EditTab( $item, $this );
				
				$user_activity_tab->setTab(
					'ja-user-activity',
					'User activity',
					'chart-line'
				);
				
				$user_activity_tab->setHandler( function() use ($item) : string {
					/**
					 * @var Customer $item
					 */
					return $this->handleUserActivity( $item );
				} );
				
				$res[] = $user_activity_tab;
				break;
				
				
				
				
			case Product::getEntityType():
				$user_activity_tab = new Admin_EntityManager_EditTabProvider_EditTab( $item, $this );
				
				$user_activity_tab->setTab(
					'ja-analytics',
					'Analytics',
					'chart-line'
				);
				
				$user_activity_tab->setHandler( function() use ($item) : string {
					/**
					 * @var Product $item
					 */
					return $this->handleProductAnalytics( $item );
				} );
				
				$res[] = $user_activity_tab;
				break;
				
				
				
			case Category::getEntityType():
				$user_activity_tab = new Admin_EntityManager_EditTabProvider_EditTab( $item, $this );
				
				$user_activity_tab->setTab(
					'ja-analytics',
					'Analytics',
					'chart-line'
				);
				
				$user_activity_tab->setHandler( function() use ($item) : string {
					/**
					 * @var Category $item
					 */
					return $this->handleCategoryAnalytics( $item );
				} );
				
				$res[] = $user_activity_tab;
				break;
				
				
				
			case Signpost::getEntityType():
				$user_activity_tab = new Admin_EntityManager_EditTabProvider_EditTab( $item, $this );
				
				$user_activity_tab->setTab(
					'ja-analytics',
					'Analytics',
					'chart-line'
				);
				
				$user_activity_tab->setHandler( function() use ($item) : string {
					/**
					 * @var Signpost $item
					 */
					return $this->handleSignpostAnalytics( $item );
				} );
				
				$res[] = $user_activity_tab;
				break;
			
			case KindOfProduct::getEntityType():
				$user_activity_tab = new Admin_EntityManager_EditTabProvider_EditTab( $item, $this );
				
				$user_activity_tab->setTab(
					'ja-analytics',
					'Analytics',
					'chart-line'
				);
				
				$user_activity_tab->setHandler( function() use ($item) : string {
					/**
					 * @var KindOfProduct $item
					 */
					return $this->handleKindOfProductAnalytics( $item );
				} );
				
				$res[] = $user_activity_tab;
				break;
		}
		
		

		return $res;
	}
	
	public function handleUserActivity( Customer $customer ) : string
	{
		//TODO:
		return 'JA';
	}
	
	
	public function handleProductAnalytics( Product $product ) : string
	{
		//TODO:
		return 'JA';
	}
	
	public function handleCategoryAnalytics( Category $product ) : string
	{
		//TODO:
		return 'JA';
	}
	
	public function handleSignpostAnalytics( Signpost $product ) : string
	{
		//TODO:
		return 'JA';
	}
	
	public function handleKindOfProductAnalytics( KindOfProduct $kind_of_product ) : string
	{
		//TODO:
		return 'JA';
	}
}
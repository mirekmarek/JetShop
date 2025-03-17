<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;


use JetApplication\CashDesk;
use JetApplication\Category_EShopData;
use JetApplication\Order;
use JetApplication\Product_EShopData;
use JetApplication\EShop_Analytics_Service;
use JetApplication\ProductListing;
use JetApplication\ShoppingCart;
use JetApplication\ShoppingCart_Item;
use JetApplication\Signpost_EShopData;


class Main extends EShop_Analytics_Service
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
		//TODO: order start
		return '';
	}
	
	public function addDeliveryInfo( CashDesk $cash_desk ) : string
	{
		//TODO: order details
		return '';
	}
	
	public function addPaymentInfo( CashDesk $cash_desk ) : string
	{
		//TODO: order details
		return '';
	}
	
	public function purchase( Order $order ) : string
	{
		//TODO: order done
		return '';
	}
	
	public function customEvent( string $evetnt, array $event_data = [] ): string
	{
		return '';
	}
	
	//TODO: search

}
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
use JetApplication\ShoppingCart;
use JetApplication\ShoppingCart_Item;
use JetApplication\Signpost_EShopData;


class Main extends EShop_Analytics_Service
{
	
	protected null|false|Session $session = null;
	
	//TODO: signopost show
	//TODO: product list show
	//TODO: filter setup
	//TODO: search
	//TODO: cart view
	//TODO: cart add
	//TODO: cart remove
	//TODO: cart update
	//TODO: order start
	//TODO: order details
	//TODO: order done
	
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
	
	
	public function viewProductsList( array $list, ?Category_EShopData $category=null, ?string $category_name='', ?int $category_id=null ) : string
	{
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
		return '';
	}
	
	public function removeFromCart( ShoppingCart_Item $cart_item ) : string
	{
		return '';
	}
	
	public function viewCart( ShoppingCart $cart ) : string
	{
		return '';
	}
	
	
	public function beginCheckout( CashDesk $cash_desk ) : string
	{
		return $this->generateEvent('InitiateCheckout', []);
	}
	
	public function addDeliveryInfo( CashDesk $cash_desk ) : string
	{
		return '';
	}
	
	public function addPaymentInfo( CashDesk $cash_desk ) : string
	{
		return '';
	}
	
	public function purchase( Order $order ) : string
	{
		return '';
	}
	
	public function customEvent( string $evetnt, array $event_data = [] ): string
	{
		return '';
	}
	

}
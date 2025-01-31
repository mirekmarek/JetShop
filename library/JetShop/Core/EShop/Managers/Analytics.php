<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\CashDesk;
use JetApplication\Category_EShopData;
use JetApplication\Order;
use JetApplication\Product_EShopData;
use JetApplication\EShop_Analytics_Service;
use JetApplication\ShoppingCart;
use JetApplication\ShoppingCart_Item;

interface Core_EShop_Managers_Analytics {
	
	/**
	 * @return EShop_Analytics_Service[]
	 */
	public function getServices() : array;
		
		
	public function header() : string;
	
	public function documentStart() : string;
	
	public function documentEnd() : string;
	
	public function catchConversionSourceInfo() : void;
	
	public function viewCategory( Category_EShopData $category ) : string;
	
	public function customEvent( string $evetnt, array $event_data=[] ) : string;
	
	/**
	 * @param array $list
	 * @param Category_EShopData|null $category
	 * @param string|null $category_name
	 * @param int|null $category_id
	 * @return string
	 */
	public function viewProductsList( array $list, ?Category_EShopData $category=null, ?string $category_name='', ?int $category_id=null ) : string;
	
	public function viewProductDetail( Product_EShopData $product ) : string;
	
	public function addToCart( ShoppingCart_Item $new_cart_item ) : string;
	
	public function removeFromCart( ShoppingCart_Item $cart_item ): string;
	
	public function viewCart( ShoppingCart $cart ) : string;
	
	public function beginCheckout( CashDesk $cash_desk ) : string;
	
	public function addDeliveryInfo( CashDesk $cash_desk ) : string;
		
	public function addPaymentInfo( CashDesk $cash_desk ) : string;
	
	public function purchase( Order $order ) : string;

}
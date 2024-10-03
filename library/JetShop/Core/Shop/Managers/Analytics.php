<?php
namespace JetShop;

use JetApplication\CashDesk;
use JetApplication\Category_ShopData;
use JetApplication\Order;
use JetApplication\Product_ShopData;
use JetApplication\Shop_Analytics_Service;
use JetApplication\ShoppingCart;
use JetApplication\ShoppingCart_Item;

interface Core_Shop_Managers_Analytics {
	
	/**
	 * @return Shop_Analytics_Service[]
	 */
	public function getServices() : array;
		
		
	public function header() : string;
	
	public function documentStart() : string;
	
	public function documentEnd() : string;
	
	/**
	 * @param array $list
	 * @param Category_ShopData|null $category
	 * @param string|null $category_name
	 * @param int|null $category_id
	 * @return string
	 */
	public function viewProducts( array $list, ?Category_ShopData $category=null, ?string $category_name='', ?int $category_id=null ) : string;
	
	public function viewProduct( Product_ShopData $product ) : string;
	
	public function addToCart( ShoppingCart_Item $new_cart_item ) : string;
	
	public function removeFromCart( ShoppingCart_Item $cart_item ): string;
	
	public function viewCart( ShoppingCart $cart ) : string;
	
	public function beginCheckout( CashDesk $cash_desk ) : string;
	
	public function addDeliveryInfo( CashDesk $cash_desk ) : string;
		
	public function addPaymentInfo( CashDesk $cash_desk ) : string;
	
	public function purchase( Order $order ) : string;

}
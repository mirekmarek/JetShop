<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Manager;


use JetApplication\CashDesk;
use JetApplication\Category_EShopData;
use JetApplication\Order;
use JetApplication\Product_EShopData;
use JetApplication\ShoppingCart;
use JetApplication\ShoppingCart_Item;

abstract class Service {
	
	protected bool $enabled = false;
	
	protected function init() : void
	{
	
	}
	
	
	public function getEnabled(): bool
	{
		return $this->enabled;
	}
	

	public function setEnabled( bool $enabled ): void
	{
		$this->enabled = $enabled;
	}
	
	
	
	public function header() : string
	{
		return '';
	}
	
	public function documentStart() : string
	{
		return '';
	}
	
	public function documentEnd() : string
	{
		return '';
	}
	
	/**
	 * @param array $list
	 * @param Category_EShopData|null $category
	 * @param string|null $category_name
	 * @param int|null $category_id
	 * @return string
	 */
	public function viewProductsList( array $list, ?Category_EShopData $category=null, ?string $category_name='', ?int $category_id=null ) : string
	{
		return '';
	}
	
	public function viewProductDetail( Product_EShopData $product ) : string
	{
		return '';
	}
	
	public function addToCart( ShoppingCart_Item $new_cart_item ) : string
	{
		return '';
	}
	
	public function removeFromCart( ShoppingCart_Item $cart_item ): string
	{
		return '';
	}
	
	public function viewCart( ShoppingCart $cart ) : string
	{
		return '';
	}
	
	public function beginCheckout( CashDesk $cash_desk ) : string
	{
		return '';
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
	
}
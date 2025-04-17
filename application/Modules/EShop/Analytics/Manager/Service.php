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
use JetApplication\ProductListing;
use JetApplication\ShoppingCart;
use JetApplication\ShoppingCart_Item;
use JetApplication\Signpost_EShopData;

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
	
	
	
	abstract public function header() : string;
	
	abstract public function documentStart() : string;
	
	abstract public function documentEnd() : string;
	
	abstract public function catchConversionSourceInfo() : void;
	
	abstract public function viewCategory( Category_EShopData $category, ?ProductListing $product_listing=null  ) : string;
	
	abstract public function viewSignpost( Signpost_EShopData $signpost ) : string;
	
	abstract public function customEvent( string $event, array $event_data=[] ) : string;
	
	abstract public function viewProductDetail( Product_EShopData $product ) : string;
	
	abstract public function addToCart( ShoppingCart_Item $new_cart_item ) : string;
	
	abstract public function removeFromCart( ShoppingCart_Item $cart_item ): string;
	
	abstract public function viewCart( ShoppingCart $cart ) : string;
	
	abstract public function beginCheckout( CashDesk $cash_desk ) : string;
	
	abstract public function checkoutInProgress( CashDesk $cash_desk ) : string;
	
	abstract public function purchase( Order $order ) : string;
	
	abstract public function searchWhisperer( string $q, array $result_ids, ?ProductListing $product_listing=null ) : string;
	
	abstract public function search( string $q, array $result_ids, ?ProductListing $product_listing=null  ) : string;
	
	
}
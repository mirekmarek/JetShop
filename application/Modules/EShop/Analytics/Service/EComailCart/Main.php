<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\EComailCart;

use JetApplication\Admin_ControlCentre;
use JetApplication\CashDesk;
use JetApplication\Category_EShopData;
use JetApplication\EShop;
use JetApplication\Order;
use JetApplication\Product_EShopData;
use JetApplication\Application_Service_EShop_AnalyticsService;
use JetApplication\Application_Service_EShop;
use JetApplication\ProductListing;
use JetApplication\ShoppingCart;
use JetApplication\ShoppingCart_Item;
use JetApplication\Signpost_EShopData;


class Main extends Application_Service_EShop_AnalyticsService
{
	protected string $id = '';
	
	public function allowed() :bool
	{
		return true;
	}
	
	public function init( EShop $eshop ) : void
	{
		parent::init( $eshop );
		$this->enabled = true;
	}
	
	
	public function initTest( EShop $eshop ) : void
	{
		parent::init( $eshop );
		$this->testing_allowed = false;
	}
	
	public function addToCart( ShoppingCart_Item $new_cart_item ) : string
	{
		$this->storeCart();
		return '';
	}
	
	public function removeFromCart( ShoppingCart_Item $cart_item ) : string
	{
		$this->storeCart();
		return '';
	}
	
	public function viewCart( ShoppingCart $cart ) : string
	{
		$this->storeCart();
		return '';
	}
	
	public function purchase( Order $order ) : string
	{
		$this->storeCart();
		return '';
	}
	
	protected function storeCart() : void
	{
		$be = Application_Service_EShop::EMailMarketingSubscribeManagerBackend();

		if(
			!$be ||
			!method_exists($be, 'storeCart')
		) {
			return;
		}
		
		$be->storeCart();
	}
	
	
	public function header(): string
	{
		return '';
	}
	
	public function documentStart(): string
	{
		return '';
	}
	
	public function documentEnd(): string
	{
		return '';
	}
	
	public function viewHomePage() : string
	{
		return '';
	}
	
	public function viewCategory( Category_EShopData $category, ?ProductListing $product_listing = null ): string
	{
		return '';
	}
	
	public function customEvent( string $event, array $event_data = [] ): string
	{
		return '';
	}
	
	public function viewProductsList( ProductListing $list, string $category_name='', string $category_id='' ) : string
	{
		return '';
	}
	
	public function viewProductDetail( Product_EShopData $product ) : string
	{
		return '';

	}
	
	public function beginCheckout( CashDesk $cash_desk ) : string
	{
		return '';
	}
	
	public function checkoutInProgress( CashDesk $cash_desk ) : string
	{
		return '';
	}
	
	
	public function viewSignpost( Signpost_EShopData $signpost ): string
	{
		return '';
	}
	
	public function searchWhisperer( string $q, array $result_ids, ?ProductListing $product_listing = null ) : string
	{
		return '';
	}
	
	public function search( string $q, array $result_ids, ?ProductListing $product_listing = null ) : string
	{
		return '';
	}
	
	
	public function getControlCentreGroup(): string
	{
		return Admin_ControlCentre::GROUP_ANALYTICS;
	}
	
	
	public function getControlCentreTitle(): string
	{
		return 'EComail - shopping cart';
	}
	
	public function getControlCentreIcon(): string
	{
		return 'cart-shopping';
	}
	
	public function getControlCentrePriority(): int
	{
		return 99;
	}
	
	public function getControlCentrePerShopMode(): bool
	{
		return true;
	}
	
}
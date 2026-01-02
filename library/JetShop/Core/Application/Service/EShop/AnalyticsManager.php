<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Service_MetaInfo;
use Jet\Application_Module;
use JetApplication\Application_Service_EShop;
use JetApplication\CashDesk;
use JetApplication\Category_EShopData;
use JetApplication\Order;
use JetApplication\Product_EShopData;
use JetApplication\Application_Service_EShop_AnalyticsService;
use JetApplication\ProductListing;
use JetApplication\ShoppingCart;
use JetApplication\ShoppingCart_Item;
use JetApplication\Signpost_EShopData;

#[Application_Service_MetaInfo(
	group: Application_Service_EShop::GROUP,
	is_mandatory: false,
	name: 'Analytics manager',
	description: '',
	module_name_prefix: 'EShop.'
)]
abstract class Core_Application_Service_EShop_AnalyticsManager extends Application_Module
{
	
	/**
	 * @return Application_Service_EShop_AnalyticsService[]
	 */
	abstract public function getServices() : array;
	
	abstract public function header() : string;
	
	abstract public function documentStart() : string;
	
	abstract public function documentEnd() : string;
	
	abstract public function catchConversionSourceInfo() : void;
	
	abstract public function viewHomePage() : string;
	
	abstract public function viewCategory( Category_EShopData $category, ?ProductListing $product_listing=null  ) : string;
	
	abstract public function viewSignpost( Signpost_EShopData $signpost ) : string;
	
	abstract public function customEvent( string $event, array $event_data=[] ) : string;
	
	abstract public function viewProductDetail( Product_EShopData $product ) : string;
	
	abstract public function addToCart( ShoppingCart_Item $new_cart_item ) : string;
	
	abstract public function removeFromCart( ShoppingCart_Item $cart_item ): string;
	
	abstract public function viewCart( ShoppingCart $cart ) : string;
	
	abstract public function beginCheckout( CashDesk $cash_desk ) : string;
	
	abstract public function checkoutInProgress( CashDesk $cash_desk ) : string;
	
	abstract public function setPurchaseHandled( Order $order ) : void;
	
	abstract public function purchase( Order $order ) : string;
	
	abstract public function searchWhisperer( string $q, array $result_ids, ?ProductListing $product_listing=null ) : string;
	
	abstract public function search( string $q, array $result_ids, ?ProductListing $product_listing=null  ) : string;
	
}
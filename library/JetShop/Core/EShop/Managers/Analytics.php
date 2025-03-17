<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use JetApplication\CashDesk;
use JetApplication\Category_EShopData;
use JetApplication\Manager_MetaInfo;
use JetApplication\Order;
use JetApplication\Product_EShopData;
use JetApplication\EShop_Analytics_Service;
use JetApplication\ProductListing;
use JetApplication\ShoppingCart;
use JetApplication\ShoppingCart_Item;
use JetApplication\Signpost_EShopData;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ESHOP,
	is_mandatory: false,
	name: 'Analytics',
	description: '',
	module_name_prefix: 'EShop.'
)]
abstract class Core_EShop_Managers_Analytics extends Application_Module
{
	
	/**
	 * @return EShop_Analytics_Service[]
	 */
	abstract public function getServices() : array;
	
	abstract public function header() : string;
	
	abstract public function documentStart() : string;
	
	abstract public function documentEnd() : string;
	
	abstract public function catchConversionSourceInfo() : void;
	
	abstract public function viewCategory( Category_EShopData $category ) : string;
	
	abstract public function viewSignpost( Signpost_EShopData $signpost ) : string;
	
	abstract public function customEvent( string $evetnt, array $event_data=[] ) : string;
	
	abstract public function viewProductsList( ProductListing $list, string $category_name='', string $category_id='' ) : string;
	
	abstract public function viewProductDetail( Product_EShopData $product ) : string;
	
	abstract public function addToCart( ShoppingCart_Item $new_cart_item ) : string;
	
	abstract public function removeFromCart( ShoppingCart_Item $cart_item ): string;
	
	abstract public function viewCart( ShoppingCart $cart ) : string;
	
	abstract public function beginCheckout( CashDesk $cash_desk ) : string;
	
	abstract public function checkoutInProgress( CashDesk $cash_desk ) : string;
	
	abstract public function purchase( Order $order ) : string;

}
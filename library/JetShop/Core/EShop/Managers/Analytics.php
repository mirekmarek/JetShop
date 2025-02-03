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
use JetApplication\ShoppingCart;
use JetApplication\ShoppingCart_Item;

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
	
	abstract public function customEvent( string $evetnt, array $event_data=[] ) : string;
	
	/**
	 * @param array $list
	 * @param Category_EShopData|null $category
	 * @param string|null $category_name
	 * @param int|null $category_id
	 * @return string
	 */
	abstract public function viewProductsList( array $list, ?Category_EShopData $category=null, ?string $category_name='', ?int $category_id=null ) : string;
	
	abstract public function viewProductDetail( Product_EShopData $product ) : string;
	
	abstract public function addToCart( ShoppingCart_Item $new_cart_item ) : string;
	
	abstract public function removeFromCart( ShoppingCart_Item $cart_item ): string;
	
	abstract public function viewCart( ShoppingCart $cart ) : string;
	
	abstract public function beginCheckout( CashDesk $cash_desk ) : string;
	
	abstract public function addDeliveryInfo( CashDesk $cash_desk ) : string;
	
	abstract public function addPaymentInfo( CashDesk $cash_desk ) : string;
	
	abstract public function purchase( Order $order ) : string;

}
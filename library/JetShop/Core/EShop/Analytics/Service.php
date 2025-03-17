<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use Jet\Application_Module_Manifest;
use Jet\Factory_MVC;
use Jet\MVC_View;
use JetApplication\CashDesk;
use JetApplication\Category_EShopData;
use JetApplication\Order;
use JetApplication\Product_EShopData;
use JetApplication\ProductListing;
use JetApplication\ShoppingCart;
use JetApplication\ShoppingCart_Item;
use JetApplication\Signpost_EShopData;

abstract class Core_EShop_Analytics_Service extends Application_Module {
	
	protected bool $enabled = false;
	protected MVC_View $view;
	
	public function __construct( Application_Module_Manifest $manifest )
	{
		parent::__construct( $manifest );
		$this->view = Factory_MVC::getViewInstance( $this->getViewsDir() );
	}
	
	abstract public function init() : void;
	
	
	public function getEnabled(): bool
	{
		return $this->enabled;
	}
	
	
	public function setEnabled( bool $enabled ): void
	{
		$this->enabled = $enabled;
	}
	
	
	public function catchConversionSourceInfo() : void
	{
	
	}
	
	
	abstract public function header() : string;
	
	abstract public function documentStart() : string;
	
	abstract public function documentEnd() : string;
	
	abstract public function viewCategory( Category_EShopData $category ) : string;
	
	abstract public function viewSignpost( Signpost_EShopData $signpost ) : string;
	
	abstract public function customEvent( string $evetnt, array $event_data=[] ) : string;
	
	abstract public function viewProductsList( ProductListing $list, string $category_name='', string $category_id='' ) : string;
	
	abstract public function viewProductDetail( Product_EShopData $product ) : string;
	
	abstract public function addToCart( ShoppingCart_Item $new_cart_item ) : string;
	
	abstract public function removeFromCart( ShoppingCart_Item $cart_item ): string;
	
	abstract public function viewCart( ShoppingCart $cart ) : string;
	
	abstract public function beginCheckout( CashDesk $cash_desk ) : string;
	
	abstract public function addDeliveryInfo( CashDesk $cash_desk ) : string;
	
	abstract public function addPaymentInfo( CashDesk $cash_desk ) : string;
	
	abstract public function purchase( Order $order ) : string;
	
	abstract public function generateEvent( string $event, array $event_data ) : string;
	
}
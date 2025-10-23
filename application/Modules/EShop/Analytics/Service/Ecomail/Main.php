<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\Ecomail;

use JetApplication\Customer;
use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\CashDesk;
use JetApplication\Category_EShopData;
use JetApplication\EShop;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
use JetApplication\Order;
use JetApplication\Product_EShopData;
use JetApplication\Application_Service_EShop_AnalyticsService;
use JetApplication\Application_Service_EShop;
use JetApplication\ProductListing;
use JetApplication\ShoppingCart;
use JetApplication\ShoppingCart_Item;
use JetApplication\Signpost_EShopData;


class Main extends Application_Service_EShop_AnalyticsService implements EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface, Admin_ControlCentre_Module_Interface
{
	use EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
	use Admin_ControlCentre_Module_Trait;
	
	protected string $js_code = '';
	protected ?string $email = null;
	
	public function allowed(): bool
	{
		return true;
	}
	
	public function init( EShop $eshop ) : void
	{
		parent::init( $eshop );
		
		$this->js_code = $this->getEshopConfig($eshop)->getJsCode();
		
		if( $this->js_code ) {
			$this->enabled = true;
		}
	}
	
	public function initTest( EShop $eshop ) : void
	{
		$this->init( $eshop );
		$this->email = 'test@test';
	}
	
	protected function getEmail() : string
	{
		if($this->email===null) {
			$this->email = Customer::getCurrentCustomer()?->getEmail()??'';
		}
		
		return $this->email;
	}
	
	
	public function header(): string
	{
		return '';
	}
	
	public function documentStart(): string
	{
		$this->view->setVar('js_code', $this->js_code);
		$this->view->setVar('email', $this->getEmail());
		$this->view->setVar('cart', Application_Service_EShop::ShoppingCart($this->eshop)->getCart());
		
		return $this->view->render('document-start');
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
		$this->view->setVar('product', $product);
		
		return $this->view->render('product-detail');

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
		return '';
	}
	
	public function checkoutInProgress( CashDesk $cash_desk ) : string
	{
		return '';
	}
	
	public function purchase( Order $order ) : string
	{
		$this->view->setVar('id', $this->js_code);
		$this->view->setVar('email', $order->getEmail() );
		$this->view->setVar('order', $order);
		
		return $this->view->render('purchase');
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
		return 'Ecomail';
	}
	
	public function getControlCentreIcon(): string
	{
		return 'chart-line';
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
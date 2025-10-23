<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\GoogleAds;

use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\CashDesk;
use JetApplication\Category_EShopData;
use JetApplication\EShop;
use JetApplication\EShop_CookieSettings_Group;
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
	
	protected string $id = '';
	protected string $send_to = '';
	protected string $conversion_js_code = '';
	protected string $page_type = 'other';
	protected array $params = [];
	protected bool $test_mode = false;
	
	public function allowed(): bool
	{
		return Application_Service_EShop::CookieSettings()?->groupAllowed(EShop_CookieSettings_Group::MARKETING);
	}
	
	public function init( EShop $eshop ) : void
	{
		parent::init( $eshop );
		
		$this->id = $this->getEshopConfig($eshop)->getAccountId();
		$this->send_to = $this->getEshopConfig($eshop)->getSendToId();
		$this->conversion_js_code = $this->getEshopConfig($eshop)->getConversionJsCode();
		
		if( $this->id ) {
			$this->enabled = true;
		}
	}
	
	public function initTest( EShop $eshop ) : void
	{
		$this->init( $eshop );
		$this->test_mode = true;
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
		$this->view->setVar('id', $this->id);
		
		$this->params['ecomm_pagetype'] = $this->page_type;
		
		$this->view->setVar('page_type', $this->page_type);
		$this->view->setVar('params', $this->params);
		
		return $this->view->render('document-end');
	}
	
	public function viewHomePage() : string
	{
		$this->page_type = 'home';
		
		if($this->test_mode) {
			$this->params = [];
			return $this->documentEnd();
		}
		
		return '';
	}
	
	public function viewCategory( Category_EShopData $category, ?ProductListing $product_listing = null ): string
	{
		$this->page_type = 'category';
		
		if($this->test_mode) {
			return $this->documentEnd();
		}
		
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
		$this->page_type = 'product';
		$this->params = [
			'ecomm_prodid' => $product->getId(),
			'ecomm_totalvalue' => $product->getPrice_WithVAT($this->pricelist)
		];
		
		if($this->test_mode) {
			return $this->documentEnd();
		}
		
		return '';
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
		$this->page_type = 'cart';
		$this->params = [
			'ecomm_prodid' => $cart->getProductIds(),
			'ecomm_totalvalue' => $cart->getAmount()
		];
		
		
		if($this->test_mode) {
			return $this->documentEnd();
		}
		
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
		$this->page_type = 'purchase';
		
		$this->view->setVar('id', $this->id);
		$this->view->setVar('send_to', $this->send_to);
		$this->view->setVar('conversion_js_code', $this->conversion_js_code );
		$this->view->setVar('order', $order);
		
		$product_ids = [];
		foreach($order->getItems() as $item) {
			if($item->isStdProduct()) {
				$product_ids[] = $item->getItemId();
			}
		}
		
		$this->params = [
			'ecomm_prodid' => $product_ids,
			'ecomm_totalvalue' => $order->getProductAmount_WithVAT()
		];
		
		
		if($this->test_mode) {
			return
				$this->view->render('purchase').
				$this->documentEnd();
		}
		
		
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
		return 'Google Ads';
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
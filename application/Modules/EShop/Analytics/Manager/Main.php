<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Manager;

use Jet\Session;
use JetApplication\CashDesk;
use JetApplication\Category_EShopData;
use JetApplication\Application_Service_EShop;
use JetApplication\EShops;
use JetApplication\Order;
use JetApplication\Product_EShopData;
use JetApplication\Application_Service_EShop_AnalyticsService;
use JetApplication\Application_Service_EShop_AnalyticsManager;
use JetApplication\ProductListing;
use JetApplication\ShoppingCart;
use JetApplication\ShoppingCart_Item;
use JetApplication\Signpost_EShopData;

class Main extends Application_Service_EShop_AnalyticsManager
{
	
	/**
	 * @var array<string,Application_Service_EShop_AnalyticsService>
	 */
	protected ?array $services = null;
	
	
	/**
	 * @return array<string,Application_Service_EShop_AnalyticsService>
	 */
	public function getServices() : array
	{
		if($this->services===null) {

			$this->services = Application_Service_EShop::list()->getList( Application_Service_EShop_AnalyticsService::class );
			
			foreach($this->services as $service) {
				$service->init( EShops::getCurrent() );
			}
		}
		
		return $this->services;
	}
	
	public function header() : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			if($service->canPerform()) {
				$res .= $service->header();
			}
		}
		return $res;
	}
	
	public function documentStart() : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			if($service->canPerform()) {
				$res .= $service->documentStart();
			}
		}
		return $res;
	}
	
	public function documentEnd() : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			if($service->canPerform()) {
				$res .= $service->documentEnd();
			}
		}
		return $res;
	}
	
	public function catchConversionSourceInfo() : void
	{
		foreach($this->getServices() as $service) {
			if($service->canPerform()) {
				$service->catchConversionSourceInfo();
			}
		}
		
	}
	
	public function viewCategory( Category_EShopData $category, ?ProductListing $product_listing=null ) : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			if($service->canPerform()) {
				$res .= $service->viewCategory( $category, $product_listing );
			}
		}
		return $res;
	}
	
	public function viewSignpost( Signpost_EShopData $signpost ) : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			if($service->canPerform()) {
				$res .= $service->viewSignpost( $signpost );
			}
		}
		return $res;
	}
	
	public function customEvent( string $event, array $event_data=[] ) : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			if($service->canPerform()) {
				$res .= $service->customEvent( $event, $event_data );
			}
		}
		return $res;
	}
	
	public function viewProductDetail( Product_EShopData $product ) : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			if($service->canPerform()) {
				$res .= $service->viewProductDetail( $product );
			}
		}
		return $res;
	}
	
	public function addToCart( ShoppingCart_Item $new_cart_item ) : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			if($service->canPerform()) {
				$res .= $service->addToCart( $new_cart_item );
			}
		}
		return $res;
	}
	
	public function removeFromCart( ShoppingCart_Item $cart_item ): string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			if($service->canPerform()) {
				$res .= $service->removeFromCart( $cart_item );
			}
		}
		return $res;
	}
	
	public function viewCart( ShoppingCart $cart ) : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			if($service->canPerform()) {
				$res .= $service->viewCart( $cart );
			}
		}
		return $res;
	}
	
	public function beginCheckout( CashDesk $cash_desk ) : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			if($service->canPerform()) {
				$res .= $service->beginCheckout( $cash_desk );
			}
		}
		return $res;
	}
	
	public function checkoutInProgress( CashDesk $cash_desk ) : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			if($service->canPerform()) {
				$res .= $service->checkoutInProgress( $cash_desk );
			}
		}
		return $res;
	}
	
	protected ?Session $purchase_event_lock_session = null;
	
	public function getPurchaseEventLockSession() : Session
	{
		if(!$this->purchase_event_lock_session) {
			$this->purchase_event_lock_session = new Session('analytics_purchase_handled_orders');
		}
		
		return $this->purchase_event_lock_session;
	}
	
	public function getPurchaseHandled( Order $order ) : bool
	{
		$session = $this->getPurchaseEventLockSession();
		$handled = $session->getValue('handled', []);
		
		return in_array($order->getId(), $handled);
	}
	
	public function setPurchaseHandled( Order $order ) : void
	{
		$session = $this->getPurchaseEventLockSession();
		$handled = $session->getValue('handled', []);
		$handled[] = $order->getId();
		
		$session->setValue('handled', $handled);
	}
	
	public function purchase( Order $order ) : string
	{
		if($this->getPurchaseHandled($order)) {
			return '<!-- P-handled -->';
		}
		
		$res = '';
		foreach($this->getServices() as $service) {
			$service->init( EShops::getCurrent() );
			if($service->canPerform()) {
				$service_code = $service->purchase( $order );
				$res .= $service_code;
				
			} else {
				$res .= '<!-- dis: '.$service->getModuleManifest()->getName().' -->';
			}
		}
		
		return $res;
	}
	
	public function searchWhisperer( string $q, array $result_ids, ?ProductListing $product_listing=null ) : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			if($service->canPerform()) {
				$res .= $service->searchWhisperer( $q, $result_ids, $product_listing );
			}
		}
		return $res;
	}
	
	public function search( string $q, array $result_ids, ?ProductListing $product_listing=null ) : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			if($service->canPerform()) {
				$res .= $service->search( $q, $result_ids, $product_listing );
			}
		}
		return $res;
	}
	
	public function viewHomePage(): string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			if($service->canPerform()) {
				$res .= $service->viewHomePage();
			}
		}
		return $res;
	}
}
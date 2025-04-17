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
use JetApplication\Managers;
use JetApplication\Order;
use JetApplication\Product_EShopData;
use JetApplication\EShop_Analytics_Service;
use JetApplication\EShop_Managers_Analytics;
use JetApplication\ProductListing;
use JetApplication\ShoppingCart;
use JetApplication\ShoppingCart_Item;
use JetApplication\Signpost_EShopData;

class Main extends EShop_Managers_Analytics
{
	
	/**
	 * @var EShop_Analytics_Service[]
	 */
	protected ?array $services = null;
	
	
	/**
	 * @return EShop_Analytics_Service[]
	 */
	public function getServices() : array
	{
		if($this->services===null) {
			$this->services = Managers::findManagers( EShop_Analytics_Service::class, 'EShop.Analytics.Service.' );
			
			foreach($this->services as $service) {
				$service->init();
			}
		}
		
		return $this->services;
	}
	
	public function header() : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			$res .= $service->header();
		}
		return $res;
	}
	
	public function documentStart() : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			$res .= $service->documentStart();
		}
		return $res;
	}
	
	public function documentEnd() : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			$res .= $service->documentEnd();
		}
		return $res;
	}
	
	public function catchConversionSourceInfo() : void
	{
		foreach($this->getServices() as $service) {
			$service->catchConversionSourceInfo();
		}
		
	}
	
	public function viewCategory( Category_EShopData $category, ?ProductListing $product_listing=null ) : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			$res .= $service->viewCategory( $category, $product_listing );
		}
		return $res;
	}
	
	public function viewSignpost( Signpost_EShopData $signpost ) : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			$res .= $service->viewSignpost( $signpost );
		}
		return $res;
	}
	
	public function customEvent( string $event, array $event_data=[] ) : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			$res .= $service->customEvent( $event, $event_data );
		}
		return $res;
	}
	
	public function viewProductDetail( Product_EShopData $product ) : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			$res .= $service->viewProductDetail( $product );
		}
		return $res;
	}
	
	public function addToCart( ShoppingCart_Item $new_cart_item ) : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			$res .= $service->addToCart( $new_cart_item );
		}
		return $res;
	}
	
	public function removeFromCart( ShoppingCart_Item $cart_item ): string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			$res .= $service->removeFromCart( $cart_item );
		}
		return $res;
	}
	
	public function viewCart( ShoppingCart $cart ) : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			$res .= $service->viewCart( $cart );
		}
		return $res;
	}
	
	public function beginCheckout( CashDesk $cash_desk ) : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			$res .= $service->beginCheckout( $cash_desk );
		}
		return $res;
	}
	
	public function checkoutInProgress( CashDesk $cash_desk ) : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			$res .= $service->checkoutInProgress( $cash_desk );
		}
		return $res;
	}
	
	
	public function purchase( Order $order ) : string
	{
		$session = new Session('analytics_purchase_handled');
		$handled = $session->getValue('handled', []);
		if(in_array($order->getId(), $handled)) {
			return '';
		}
		
		$handled[] = $order->getId();
		$session->setValue('handled', $handled);
		
		$res = '';
		foreach($this->getServices() as $service) {
			$res .= $service->purchase( $order );
		}
		
		return $res;
	}
	
	public function searchWhisperer( string $q, array $result_ids, ?ProductListing $product_listing=null ) : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			$res .= $service->searchWhisperer( $q, $result_ids, $product_listing );
		}
		return $res;
	}
	
	public function search( string $q, array $result_ids, ?ProductListing $product_listing=null ) : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			$res .= $service->search( $q, $result_ids, $product_listing );
		}
		return $res;
	}
	
}
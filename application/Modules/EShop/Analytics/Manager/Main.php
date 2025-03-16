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
	
	public function viewCategory( Category_EShopData $category ) : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			$res .= $service->viewCategory( $category );
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
	
	public function customEvent( string $evetnt, array $event_data=[] ) : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			$res .= $service->customEvent( $evetnt, $event_data );
		}
		return $res;
	}
	
	
	
	/**
	 * @param array $list
	 * @param Category_EShopData|null $category
	 * @param string|null $category_name
	 * @param int|null $category_id
	 * @return string
	 */
	public function viewProductsList( array $list, ?Category_EShopData $category=null, ?string $category_name='', ?int $category_id=null ) : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			$res .= $service->viewProductsList( $list, $category, $category_name, $category_id );
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
	
	public function addDeliveryInfo( CashDesk $cash_desk ) : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			$res .= $service->addDeliveryInfo( $cash_desk );
		}
		return $res;
	}
	
	public function addPaymentInfo( CashDesk $cash_desk ) : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			$res .= $service->addPaymentInfo( $cash_desk );
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
	
}
<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Shop\Analytics\Manager;

use Jet\Application_Module;
use JetApplication\CashDesk;
use JetApplication\Category_ShopData;
use JetApplication\Managers;
use JetApplication\Order;
use JetApplication\Product_ShopData;
use JetApplication\Shop_Analytics_Service;
use JetApplication\Shop_Managers_Analytics;
use JetApplication\ShoppingCart;
use JetApplication\ShoppingCart_Item;
use JetApplicationModule\Shop\Analytics\Service;

class Main extends Application_Module implements Shop_Managers_Analytics
{
	
	/**
	 * @var Shop_Analytics_Service[]
	 */
	protected ?array $services = null;
	
	
	/**
	 * @return Shop_Analytics_Service[]
	 */
	public function getServices() : array
	{
		if($this->services===null) {
			$this->services = Managers::findManagers( Shop_Analytics_Service::class, 'Shop.Analytics.Service.' );
			
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
	
	
	/**
	 * @param array $list
	 * @param Category_ShopData|null $category
	 * @param string|null $category_name
	 * @param int|null $category_id
	 * @return string
	 */
	public function viewProducts( array $list, ?Category_ShopData $category=null, ?string $category_name='', ?int $category_id=null ) : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			$res .= $service->viewProducts( $list, $category, $category_name, $category_id );
		}
		return $res;
	}
	
	public function viewProduct( Product_ShopData $product ) : string
	{
		$res = '';
		foreach($this->getServices() as $service) {
			$res .= $service->viewProduct( $product );
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
		$res = '';
		foreach($this->getServices() as $service) {
			$res .= $service->purchase( $order );
		}
		return $res;
	}
	
}
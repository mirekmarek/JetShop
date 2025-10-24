<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Discounts\FreeDelivery;


use Jet\Auth;
use Jet\Factory_MVC;
use Jet\MVC;
use Jet\Tr;
use JetApplication\Application_Service_EShop;
use JetApplication\CashDesk;
use JetApplication\Delivery_Method;
use JetApplication\EShop_Pages;
use JetApplication\Marketing_DeliveryFeeDiscount;
use JetApplication\Application_Service_EShop_DiscountModule_DeliveryFee;
use JetApplication\Order;
use JetApplication\Order_Item;
use JetApplication\Customer;

class Main extends Application_Service_EShop_DiscountModule_DeliveryFee
{
	protected ?array $marketing_discounts = null;
	
	/**
	 * @return Marketing_DeliveryFeeDiscount[]
	 */
	public function getMarketingDiscounts(): array
	{
		if($this->marketing_discounts === null) {
			$_marketing_discounts = Marketing_DeliveryFeeDiscount::getAllActive();
			
			$this->marketing_discounts = [];
			foreach($_marketing_discounts as $discount) {
				if(
					$discount->isActive() &&
					$discount->isRelevant( $this->cash_desk->getCart()->getProductIds() )
				) {
					$this->marketing_discounts[$discount->getDeliveryMethodId()] = $discount;
				}
			}
			
		}
		
		return $this->marketing_discounts;
	}
	
	
	public function handleShoppingCart( CashDesk $cash_desk ): string
	{
		$this->setCashDesk( $cash_desk );
		
		$view = Factory_MVC::getViewInstance($this->getViewsDir());
		
		$view->setVar('module', $this );
		$view->setVar('cash_desk', $cash_desk);
		
		
		
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() use ($view) {
				return $view->render('shopping-cart-status');
			}
		);
	}
	
	protected function getOrderAmount() : float
	{
		return $this->cash_desk->getCart()->getAmount();
	}
	
	public function customerIsRegistered(): bool
	{
		/**
		 * @var Customer $customer
		 */
		$customer = Auth::getCurrentUser();
		
		if(
			$customer &&
			$customer->getMailingSubscribed()
		) {
			return true;
		}
		
		
		if( MVC::getPage()->getId()==EShop_Pages::CashDesk()->getId() ) {
			$cash_desk = Application_Service_EShop::CashDesk()->getCashDesk();
			
			if(
				!$cash_desk->getNoRegistration() &&
				$cash_desk->getAgreeFlagChecked( 'mailing_subscribe' )
			) {
				return true;
			}
		}
		
		
		return false;
	}
	
	protected function getFreeLimit( Delivery_Method $method ): float
	{
		
		if( $this->customerIsRegistered() ) {
			return $method->getFreeDeliveryLimitRegisteredCustomer();
		}
		
		return $method->getFreeDeliveryLimit();
	}
	
	public function generateDiscounts( CashDesk $cash_desk ): void
	{
		$this->setCashDesk($cash_desk);
		
		$amount = $this->getOrderAmount();
		if(!$amount) {
			return;
		}
		
		$pricelist = $cash_desk->getPricelist();
		
		
		$marketing_discounts = $this->getMarketingDiscounts();
		
		foreach($cash_desk->getAvailableDeliveryMethods() as $method) {
			$method->setPrice( $pricelist, $method->getDefaultPrice( $pricelist ) );
			
			if($method->getDiscountIsNotAllowed()) {
				continue;
			}
			
			if(isset($marketing_discounts[$method->getId()])) {
				$discount = $marketing_discounts[$method->getId()];
				
				$mtp = (100-$discount->getDiscountPercentage())/100;
				
				if(
					$discount->getAmountLimit()>0 &&
					$discount->getAmountLimit()>$amount
				) {
					$mtp = 1;
				}
				
				if($mtp<1) {
					
					$price = $pricelist->round( $method->getDefaultPrice( $pricelist ) * $mtp );
					
					if($price<$method->getPrice($pricelist)) {
						$method->setPrice( $cash_desk->getPricelist(), $price );
					}
				}
			}
			
			$free_limit = $this->getFreeLimit( $method );
			
			if(
				$free_limit>0 &&
				$amount>$free_limit
			) {
				$method->setPrice( $cash_desk->getPricelist(), 0 );
			}
			
		}
	}
	
	public function limit() : float|false
	{
		$limit = false;
		foreach($this->cash_desk->getAvailableDeliveryMethods() as $method) {
			if(
				$method->getDiscountIsNotAllowed()
			) {
				continue;
			}
			
			$free_limit = $this->getFreeLimit( $method );
			if(!$free_limit) {
				continue;
			}
			
			if( $limit===false || $limit>$free_limit ) {
				$limit = $free_limit;
			}
		}
		

		return $limit;
	}
	
	public function limitRegistered() : float|false
	{
		$limit = false;
		foreach($this->cash_desk->getAvailableDeliveryMethods() as $method) {
			if(
				$method->getDiscountIsNotAllowed()
			) {
				continue;
			}
			
			$free_limit = $method->getFreeDeliveryLimitRegisteredCustomer();
			if(!$free_limit) {
				continue;
			}
			
			if( $limit===false || $limit>$free_limit ) {
				$limit = $free_limit;
			}
		}
		
		
		return $limit;
	}
	
	
	public function remains() : float|false
	{
		$limit = $this->limit();
		
		if($limit===false) {
			return false;
		}
		
		return $limit - $this->getOrderAmount();
	}

	
	public function checkDiscounts( CashDesk $cash_desk ): void
	{
	}
	
	public function Order_newOrderCreated( Order $order ): void
	{
	}
	
	public function Order_canceled( Order $order ): void
	{
	}
	
	public function Order_itemRemoved( Order $order, Order_Item $item ): void
	{
	}
	
	public function Order_itemAdded( Order $order, Order_Item $item ): void
	{
	}
	
	public function Order_reactivated( Order $order, Order_Item $item ): void
	{
	}
}
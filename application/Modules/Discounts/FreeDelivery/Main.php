<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Discounts\FreeDelivery;


use Jet\Factory_MVC;
use Jet\Tr;
use JetApplication\CashDesk;
use JetApplication\Discounts_Module;
use JetApplication\Marketing_DeliveryFeeDiscount;
use JetApplication\Order;
use JetApplication\Order_Item;
use JetApplication\Pricelists;
use JetApplication\EShop_Managers;


class Main extends Discounts_Module
{
	
	public function ShoppingCart_handle(): string
	{
		$view = Factory_MVC::getViewInstance($this->getViewsDir());
		
		$view->setVar('remains', $this->remains());
		
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() use ($view) {
				return $view->render('shopping-cart-status');
			}
		);
	}
	
	public function CashDesk_RegisteredCustomer_handle(): string
	{
		return '';
	}
	
	protected function getOrderAmount() : float
	{
		return EShop_Managers::ShoppingCart()->getCart()->getAmount();
	}
	
	public function generateDiscounts( CashDesk $cash_desk ): void
	{
		$amount = $this->getOrderAmount();
		
		$_marketing_discounts = Marketing_DeliveryFeeDiscount::getAllActive();
		
		$marketing_discounts = [];
		foreach($_marketing_discounts as $discount) {
			if(
				$discount->isActive() &&
				$discount->getDiscountPercentage()>0 &&
				$discount->isRelevant( EShop_Managers::ShoppingCart()->getCart()->getProductIds() )
			) {
				$marketing_discounts[$discount->getDeliveryMethodId()] = $discount;
			}
		}
		
		foreach($cash_desk->getAvailableDeliveryMethods() as $method) {
			if(
				!$method->getDiscountIsNotAllowed() &&
				$method->getFreeDeliveryLimit()>0 &&
				$amount>$method->getFreeDeliveryLimit()
			) {
				$method->setPrice( $cash_desk->getPricelist(), 0 );
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
					$pricelist = Pricelists::getCurrent();
					
					$price = $pricelist->round( $method->getDefaultPrice( $pricelist ) * $mtp );
					
					if($price<$method->getPrice($pricelist)) {
						$method->setPrice( $cash_desk->getPricelist(), $price );
					}
				}
			}
		}
	}
	
	public function remains() : float|bool
	{
		$cash_desk = EShop_Managers::CashDesk()->getCashDesk();
		
		$limit = false;
		foreach($cash_desk->getAvailableDeliveryMethods() as $method) {
			if(
				$method->getDiscountIsNotAllowed() ||
				$method->getFreeDeliveryLimit()==0
			) {
				continue;
			}
			
			if($limit===false || $limit>$method->getFreeDeliveryLimit()) {
				$limit = $method->getFreeDeliveryLimit();
			}
		}
		
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
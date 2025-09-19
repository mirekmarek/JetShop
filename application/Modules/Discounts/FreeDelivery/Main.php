<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Discounts\FreeDelivery;


use Jet\Factory_MVC;
use Jet\Tr;
use JetApplication\CashDesk;
use JetApplication\Marketing_DeliveryFeeDiscount;
use JetApplication\Application_Service_EShop_DiscountModule_DeliveryFee;
use JetApplication\Order;
use JetApplication\Order_Item;
use JetApplication\Pricelists;

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
		
		$view->setVar('limit', $this->limit());
		$view->setVar('remains', $this->remains());
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
	
	public function generateDiscounts( CashDesk $cash_desk ): void
	{
		$this->setCashDesk($cash_desk);
		
		$amount = $this->getOrderAmount();
		if(!$amount) {
			return;
		}
		
		$marketing_discounts = $this->getMarketingDiscounts();
		
		
		foreach($cash_desk->getAvailableDeliveryMethods() as $method) {
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
					$pricelist = Pricelists::getCurrent();
					
					$price = $pricelist->round( $method->getDefaultPrice( $pricelist ) * $mtp );
					
					if($price<$method->getPrice($pricelist)) {
						$method->setPrice( $cash_desk->getPricelist(), $price );
					}
				}
			}
			
			if(
				$method->getFreeDeliveryLimit()>0 &&
				$amount>$method->getFreeDeliveryLimit()
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
				$method->getDiscountIsNotAllowed() ||
				$method->getFreeDeliveryLimit()==0
			) {
				continue;
			}
			
			if($limit===false || $limit>$method->getFreeDeliveryLimit()) {
				$limit = $method->getFreeDeliveryLimit();
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
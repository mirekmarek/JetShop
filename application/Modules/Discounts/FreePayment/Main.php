<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Discounts\FreePayment;


use JetApplication\CashDesk;
use JetApplication\Discounts_Module;
use JetApplication\Order;
use JetApplication\Order_Item;
use JetApplication\EShop_Managers;


class Main extends Discounts_Module
{
	
	public function ShoppingCart_handle(): string
	{
		return '';
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
		
		foreach($cash_desk->getAvailablePaymentMethods() as $method) {
			if(
				$method->getDiscountIsNotAllowed() ||
				$method->getFreePaymentLimit()==0 ||
				$method->getFreePaymentLimit()>$amount
			) {
				continue;
			}
			
			$method->setPrice( $cash_desk->getPricelist(), 0 );
		}
	}
	
	public function remains() : float|bool
	{
		$cash_desk = EShop_Managers::CashDesk()->getCashDesk();
		
		$limit = false;
		foreach($cash_desk->getAvailablePaymentMethods() as $method) {
			if(
				$method->getDiscountIsNotAllowed() ||
				$method->getFreePaymentLimit()==0
			) {
				continue;
			}
			
			if($limit===false || $limit>$method->getFreePaymentLimit()) {
				$limit = $method->getFreePaymentLimit();
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
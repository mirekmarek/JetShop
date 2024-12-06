<?php
namespace JetApplicationModule\EShop\CashDesk;

use Jet\Data_DateTime;
use Jet\Http_Request;

use JetApplication\Discounts;
use JetApplication\Marketing_ConversionSourceDetector;
use JetApplication\Order;
use JetApplication\Customer;
use JetApplication\EShop_Managers;
use JetApplication\Order_Item;

trait CashDesk_Order {

	public function getOrder() : Order
	{


		$delivery_method = $this->getSelectedDeliveryMethod();
		$payment_method = $this->getSelectedPaymentMethod();
		$billing_address = $this->getBillingAddress();
		$delivery_address = $this->getDeliveryAddress();

		$customer = Customer::getCurrentCustomer();

		$order = new Order();

		
		$order->setEshop( $this->eshop );
		
		$order->setConversionSource( Marketing_ConversionSourceDetector::getDetectedSources() );
		
		$order->setCurrencyCode( $this->pricelist->getCurrency()->getCode() );
		$order->setPricelistCode( $this->pricelist->getCode() );
		$order->setAvailabilityCode( $this->availability->getCode() );
		
		$order->setIpAddress( Http_Request::clientIP() );
		$order->setDatePurchased( Data_DateTime::now() );
		if($customer) {
			$order->setCustomerId( $customer->getId() );
		}

		$order->setEmail( $this->getEmailAddress() );
		$order->setPhone( $this->getPhoneWithPrefix() );

		$order->setBillingAddress( $billing_address );

		if($delivery_address) {
			$order->setDeliveryAddress( $delivery_address );
		}


		$order->setSpecialRequirements( $this->getSpecialRequirements() );
		$order->setDifferentDeliveryAddress( $this->hasDifferentDeliveryAddress() );
		$order->setCompanyOrder( $this->isCompanyOrder() );

		foreach($this->getAgreeFlags() as $flag) {
			$flag->setOrderState( $order );
		}

		$cart = EShop_Managers::ShoppingCart()->getCart();
		
		foreach($cart->getItems() as $cart_item)
		{
			$order_item = new Order_Item();
			$order_item->setupProduct( $order->getPricelist(), $cart_item->getProduct(), $cart_item->getNumberOfUnits() );
			$order->addItem( $order_item );
		}
		
		if(
			$delivery_method->isPersonalTakeover() &&
			($place = $this->getSelectedPersonalTakeoverDeliveryPoint())
		) {
			$point_code = $place->getPointCode();
		} else {
			$point_code = '';
		}
		
		$order->setDeliveryMethod( $delivery_method, $point_code );

		if(($payment_option=$this->getSelectedPaymentMethodOption())) {
			$payment_option = $payment_option->getId();
		} else {
			$payment_option = 0;
		}
		
		$order->setPaymentMethod( $payment_method, $payment_option );
		
		

		foreach($cart->getAllSelectedGifts() as $gift) {
			
			$gift_order_item = new Order_Item();
			$gift_order_item->setupGift( $order->getPricelist(), $gift->getProduct(), $gift->getNumberOfUnits() );
			
			$order->addItem( $gift_order_item );
		}

		foreach( $this->getDiscounts() as $discount ) {
			$discount_order_item = new Order_Item();
			$discount_order_item->setupDiscount( $order->getPricelist(), $discount );
			$order->addItem( $discount_order_item );
		}
		
		$order->recalculate();
		
		return $order;
	}

	public function saveOrder() : ?Order
	{

		if(!$this->isDone()) {
			return null;
		}
		
		$this->registerCustomer();
		$this->saveCustomerAddresses();
		
		$order = $this->getOrder();
		$order->save();
		
		foreach($this->getAgreeFlags() as $flag) {
			$flag->onOrderSave( $order );
		}
		
		foreach(Discounts::Manager()->getActiveModules() as $dm) {
			$dm->Order_newOrderCreated( $order );
		}
		
		$order->newOrder();
		
		EShop_Managers::ShoppingCart()->resetCart();
		$this->reset();
		
		return $order;
	}

}
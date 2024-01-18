<?php
namespace JetApplicationModule\Shop\CashDesk;

use Jet\Data_DateTime;
use Jet\Http_Request;

use JetApplication\Order;
use JetApplication\Customer;
use JetApplication\Order_Status;
use JetApplication\Order_Status_Kind;
use JetApplication\Shop_Managers;
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

		$order->setShop( $this->shop );
		$order->setIpAddress( Http_Request::clientIP() );
		$order->setDatePurchased( Data_DateTime::now() );
		if($customer) {
			$order->setCustomerId( $customer->getId() );
		}

		$order->setEmail( $this->getEmailAddress() );
		$order->setPhone( $this->getPhone() );

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

		$cart = Shop_Managers::ShoppingCart()->getCart();
		
		foreach($cart->getItems() as $cart_item)
		{
			$product_sq = $cart_item->getProduct()->getInStockQty();
			$cart_q = $cart_item->getQuantity();
			
			
			if(
				$cart_q>$product_sq &&
				$product_sq>0
			) {
				$order_item = new Order_Item();
				$order_item->setupByCartItem( $cart_item, $product_sq, true );
				$order->addItem( $order_item );
				
				$order_item = new Order_Item();
				$order_item->setupByCartItem( $cart_item, $cart_q - $product_sq, false );
				$order->addItem( $order_item );
				
				
			} else {
				$order_item = new Order_Item();
				$order_item->setupByCartItem( $cart_item, $cart_q, $product_sq>0 );
				$order->addItem( $order_item );
			}
			
			
		}
		
		
		$delivery_order_item = new Order_Item();
		$delivery_order_item->setupByDeliveryMethod( $delivery_method );
		
		$order->setDeliveryMethodId( $delivery_method->getId() );
		if($delivery_method->isPersonalTakeover()) {
			$place = $this->getSelectedPersonalTakeoverPlace();
			$order->setDeliveryPersonalTakeoverPlaceCode( $place->getPlaceCode() );
			$delivery_order_item->setSubCode( $place->getPlaceCode() );
		}
		
		$order->addItem( $delivery_order_item );
		
		
		
		$payment_order_item = new Order_Item();
		$payment_order_item->setupByPaymentMethod( $payment_method );
		
		$order->setPaymentMethodId( $payment_method->getId() );
		if(($payment_option=$this->getSelectedPaymentMethodOption())) {
			$order->setPaymentMethodSpecification( $payment_option->getId() );
			$payment_order_item->setSubCode($payment_option->getId());
			$payment_order_item->setDescription( $payment_option->getTitle() );
		}
		
		$order->addItem( $payment_order_item );

		//TODO: services
		//TODO: gifts

		foreach( $this->getDiscounts() as $discount ) {
			$discount_order_item = new Order_Item();
			$discount_order_item->setupByDiscount( $discount );
			$order->addItem( $discount_order_item );
		}
		
		$order->recalculate();
		
		if($this->getSelectedPaymentMethod()->getKind()->isOnlinePayment()) {
			$status_id = Order_Status::getDefault( Order_Status_Kind::KIND_WAITING_FOR_PAYMENT )->getId();
		} else {
			$status_id = Order_Status::getDefault( Order_Status_Kind::KIND_NEW )->getId();
		}
		
		$order->setStatus(
			status_id: $status_id,
			customer_notified: true,
			comment: $this->getSpecialRequirements(),
			administrator: '',
			administrator_id: 0,
			comment_is_visible_for_customer: true
		);

		return $order;
	}

	public function saveOrder() : ?Order
	{

		if(!$this->isDone()) {
			return null;
		}

		$order = $this->getOrder();

		$this->registerCustomer();
		$this->saveCustomerAddresses();

		$order->save();

		foreach($this->getAgreeFlags() as $flag) {
			$flag->onOrderSave( $order );
		}
		
		$order->onNewOrderSave();
		
		$this->afterOrderSave( $order );
		
		return $order;
	}
	
	public function afterOrderSave( Order $order ) : void
	{
		$session = $this->getSession();
		$session->reset();
		$session->setValue('last_created_order_id', $order->getId());
		
		Shop_Managers::ShoppingCart()->getCart()->reset();
		Shop_Managers::ShoppingCart()->saveCart();
		
		if(Customer::getCurrentCustomer()) {
			$this->onCustomerLogin();
		}
		
		$this->setCurrentStep( CashDesk::STEP_DELIVERY );
		
	}


}
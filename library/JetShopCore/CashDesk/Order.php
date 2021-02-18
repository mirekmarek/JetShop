<?php
namespace JetShop;

use Jet\Data_DateTime;
use Jet\Http_Request;

trait Core_CashDesk_Order {

	public function getOrder() : Order
	{
		/**
		 * @var CashDesk $this
		 */

		$delivery_method = $this->getSelectedDeliveryMethod();
		$payment_method = $this->getSelectedPaymentMethod();
		$billing_address = $this->getBillingAddress();
		$delivery_address = $this->getDeliveryAddress();

		$customer = Customer::getCurrentCustomer();

		$order = new Order();

		$order->setShopCode( $this->getShopCode() );
		$order->setIpAddress( Http_Request::clientIP() );
		$order->setDatePurchased( Data_DateTime::now() );
		if($customer) {
			$order->setCustomerId( $customer->getId() );
		}

		$order->setEmail( $this->getEmailAddress() );
		$order->setPhone( $this->getPhone() );

		$order->setBillingCompanyName( $billing_address->getCompanyName() );
		$order->setBillingCompanyId( $billing_address->getCompanyId() );
		$order->setBillingCompanyVatId( $billing_address->getCompanyVatId() );
		$order->setBillingFirstName( $billing_address->getFirstName() );
		$order->setBillingSurname( $billing_address->getSurname() );
		$order->setBillingAddressStreetNo( $billing_address->getAddressStreetNo() );
		$order->setBillingAddressTown( $billing_address->getAddressTown() );
		$order->setBillingAddressZip( $billing_address->getAddressZip() );
		$order->setBillingAddressCountry( $billing_address->getAddressCountry() );


		if($delivery_address) {
			$order->setDeliveryCompanyName( $delivery_address->getCompanyName() );
			$order->setDeliveryFirstName( $delivery_address->getFirstName() );
			$order->setDeliverySurname( $delivery_address->getSurname() );
			$order->setDeliveryAddressStreetNo( $delivery_address->getAddressStreetNo() );
			$order->setDeliveryAddressTown( $delivery_address->getAddressTown() );
			$order->setDeliveryAddressZip( $delivery_address->getAddressZip() );
			$order->setDeliveryAddressCountry( $delivery_address->getAddressCountry() );
		}


		$order->setSpecialRequirements( $this->getSpecialRequirements() );
		$order->setDifferentDeliveryAddress( $this->hasDifferentDeliveryAddress() );
		$order->setCompanyOrder( $this->isCompanyOrder() );

		foreach($this->getAgreeFlags() as $flag) {
			$flag->setOrderState( $order );
		}

		foreach(ShoppingCart::get()->getOrderItems() as $order_item)
		{
			$order->addItem( $order_item );
		}


		$order->setDeliveryMethodCode( $delivery_method->getCode() );
		if($delivery_method->isPersonalTakeover()) {
			$order->setDeliveryPersonalTakeoverPlaceCode( $this->getSelectedPersonalTakeoverPlace()->getPlaceCode() );
		}
		$order->addItem( $delivery_method->getOrderItem( $this ) );

		$order->setPaymentMethodCode( $payment_method->getCode() );
		//TODO: payment specifications
		$order->addItem( $payment_method->getOrderItem( $this ) );

		//TODO: services
		//TODO: gifts

		foreach(Discounts::getActiveModules() as $dm) {
			foreach( $dm->getDiscounts() as $discount ) {
				$discount->setType( Order_Item::ITEM_TYPE_DISCOUNT );
				$order->addItem($discount);
			}
		}

		//TODO: status_code - default status id

		$order->recalculate();

		return $order;
	}

	public function saveOrder() : bool
	{
		/**
		 * @var CashDesk $this
		 */

		if(!$this->isDone()) {
			return false;
		}

		$order = $this->getOrder();

		$this->registerCustomer();
		$this->saveCustomerAddresses();

		$order->save();

		$session = $this->getSession();
		$session->reset();
		$session->setValue('last_created_order_id', $order->getId());

		ShoppingCart::get()->reset();

		if(Customer::getCurrentCustomer()) {
			$this->onCustomerLogin();
		}

		$this->setCurrentStep( CashDesk::STEP_DELIVERY );

		foreach(Discounts::getActiveModules() as $dm) {
			$dm->Order_saved( $order );
		}

		//TODO: emit event
		//TODO: process payment

		die('??');

		return true;
	}


}
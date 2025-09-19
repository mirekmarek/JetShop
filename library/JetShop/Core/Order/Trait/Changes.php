<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Auth;
use Jet\Data_DateTime;
use Jet\Logger;
use JetApplication\Application_Service_Admin;
use JetApplication\Customer;
use JetApplication\Delivery_Method;
use JetApplication\Discounts_Discount;
use JetApplication\EShopEntity_Address;
use JetApplication\Order;
use JetApplication\Order_ChangeHistory;
use JetApplication\Order_Item;
use JetApplication\Payment_Method;
use JetApplication\Product_EShopData;

trait Core_Order_Trait_Changes {
	
	public function startChange() : Order_ChangeHistory
	{
		$change = new Order_ChangeHistory();

		$change->setOrder( $this );
		$change->setDateAdded( Data_DateTime::now() );
		
		$admin = Auth::getCurrentUser();
		if($admin) {
			$change->setAdministrator( $admin->getName() );
			$change->setAdministratorId( $admin->getId() );
		}
		
		return $change;
	}
	
	public function deleteItem( Order_Item $item ) : Order_ChangeHistory
	{
		
		$change = $this->startChange();
		$change->addChange(
			property: 'idem_deleted',
			old_value:
			$item->getTitle().' ('.$item->getNumberOfUnits().' '.$item->getMeasureUnit()?->getName().' '.Application_Service_Admin::PriceFormatter()->formatWithCurrency($this->getPricelist(), $item->getPricePerUnit()).')',
			new_value: ''
		);
		$change->save();
		
		
		$item->delete();
		unset( $this->items[$item->getId()] );
		$this->recalculate();
		$this->save();
		
		Logger::info(
			event: 'order_item_deleted',
			event_message: 'Order ('.$this->getNumber().') item has been deleted',
			context_object_id: $this->getId(),
			context_object_name: $this->getNumber(),
			context_object_data: [
				'order_item' => $item
			]
		);
		
		$this->updated( $change );
		
		return $change;
	}
	
	public function changeItemQty( Order_Item $item, float $new_number_of_units ) : Order_ChangeHistory
	{
		$old = $item->getTitle().': '.$item->getNumberOfUnits().' '.$item->getMeasureUnit()?->getName();
		$item->setNumberOfUnits( $new_number_of_units, $item->getMeasureUnit() );
		$new = $item->getTitle().': '.$item->getNumberOfUnits().' '.$item->getMeasureUnit()?->getName();
		
		
		$change = $this->startChange();
		$change->addChange(
			property: 'item_number_of_units_updated',
			old_value: $old,
			new_value: $new
		);
		$change->save();
		
		
		$item->save();
		$this->recalculate();
		$this->save();
		
		Logger::info(
			event: 'order_item_number_of_units_updated',
			event_message: 'Order ('.$this->getNumber().') item number of units has been updated',
			context_object_id: $this->getId(),
			context_object_name: $this->getNumber(),
			context_object_data: [
				'order_item' => $item
			]
		);
		
		$this->updated( $change );
		
		return $change;
	}
	
	
	public function changeItemsQty( array $items ) : Order_ChangeHistory
	{
		$change = $this->startChange();
		
		foreach($this->getItems() as $item) {
			if(!isset($items[$item->getId()])) {
				continue;
			}
			
			$new_qty = $items[$item->getId()];
			
			if($new_qty==$item->getNumberOfUnits()) {
				continue;
			}
			
			if(!$new_qty) {
				$change->addChange(
					property: 'idem_deleted',
					old_value:
					$item->getTitle().' ('.$item->getNumberOfUnits().' '.$item->getMeasureUnit()?->getName().' '.Application_Service_Admin::PriceFormatter()->formatWithCurrency($this->getPricelist(), $item->getPricePerUnit()).')',
					new_value: ''
				);
				
				$item->delete();
				unset( $this->items[$item->getId()] );
				
				
			} else {
				$old = $item->getTitle().': '.$item->getNumberOfUnits().' '.$item->getMeasureUnit()?->getName();
				$item->setNumberOfUnits( $new_qty, $item->getMeasureUnit() );
				$new = $item->getTitle().': '.$item->getNumberOfUnits().' '.$item->getMeasureUnit()?->getName();
				
				$change->addChange(
					property: 'item_number_of_units_updated',
					old_value: $old,
					new_value: $new
				);
				$change->save();
				
				
				$item->save();
			}
			
		}
		
		if($change->hasChange()) {
			$this->recalculate();
			$this->save();
			$change->save();
			
			$this->updated( $change );
			
			$this->checkIsReady();
		}
		
		return $change;
	}
	
	
	public function addProduct( Product_EShopData $product, float $number_of_units ) : Order_ChangeHistory
	{
		/**
		 * @var Order $this
		 */
		
		$in_stock = $product->getNumberOfAvailable( $this->getAvailability() );
		
		$new_item = new Order_Item();
		$new_item->setupProduct( $this->getPricelist(), $product, $number_of_units );
		
		$this->addItem( $new_item );
		$this->recalculate();
		$this->save();
		
		
		$change = $this->startChange();
		$change->addChange('product_added', '', $new_item->getNumberOfUnits().' '.$new_item->getMeasureUnit()?->getName().' '.$new_item->getTitle() );
		$change->save();
		
		Logger::info(
			event: 'order_item_added:product',
			event_message: 'Order ('.$this->getNumber().') item:product has been added',
			context_object_id: $this->getId(),
			context_object_name: $this->getNumber(),
			context_object_data: [
				'order_item' => $new_item
			]
		);
		
		$this->updated( $change );
		
		return $change;
	}
	
	
	public function addGift( Product_EShopData $product, float $number_of_units ) : ?Order_ChangeHistory
	{
		/**
		 * @var Order $this
		 */
		
		$in_stock = $product->getNumberOfAvailable( $this->getAvailability() );
		
		$new_item = new Order_Item();
		$new_item->setupGift( $this->getPricelist(), $product, $number_of_units );
		
		$this->addItem( $new_item );
		$this->recalculate();
		$this->save();
		
		
		$change = $this->startChange();
		$change->addChange('gift_added', '', $new_item->getNumberOfUnits().' '.$new_item->getMeasureUnit()?->getName().' '.$new_item->getTitle() );
		$change->save();
		
		Logger::info(
			event: 'order_item_added:product',
			event_message: 'Order ('.$this->getNumber().') item:gift has been added',
			context_object_id: $this->getId(),
			context_object_name: $this->getNumber(),
			context_object_data: [
				'order_item' => $new_item
			]
		);
		
		$this->updated( $change );
		
		return $change;
	}
	
	public function addDeliveryFee( Delivery_Method $method, float $fee ) : Order_ChangeHistory
	{
		/**
		 * @var Order $this
		 */
		
		$method->getPriceEntity( $this->getPricelist() )->setPrice( $fee );
		
		$new_item = new Order_Item();
		$new_item->setupDeliveryMethod( $this->getPricelist(), $method );
		
		$this->addItem( $new_item );
		$this->recalculate();
		$this->save();
		
		
		$change = $this->startChange();
		$change->addChange('delivery_fee_added', '', $new_item->getNumberOfUnits().'x '.$new_item->getTitle() );
		$change->save();
		
		Logger::info(
			event: 'order_item_added:delivery_fee',
			event_message: 'Order ('.$this->getNumber().') item:delivery_fee has been added',
			context_object_id: $this->getId(),
			context_object_name: $this->getNumber(),
			context_object_data: [
				'order_item' => $new_item
			]
		);
		
		$this->updated( $change );
		
		return $change;
	}
	
	
	public function addPaymentFee( Payment_Method $method, float $fee ) : Order_ChangeHistory
	{
		/**
		 * @var Order $this
		 */
		
		$method->getPriceEntity($this->getPricelist())->setPrice( $fee );
		
		
		$new_item = new Order_Item();
		$new_item->setupPaymentMethod( $this->getPricelist(), $method );
		
		$this->addItem( $new_item );
		$this->recalculate();
		$this->save();
		
		
		$change = $this->startChange();
		$change->addChange('payment_fee_added', '', $new_item->getNumberOfUnits().'x '.$new_item->getTitle() );
		$change->save();
		
		Logger::info(
			event: 'order_item_added:payment_fee',
			event_message: 'Order ('.$this->getNumber().') item:payment_fee has been added',
			context_object_id: $this->getId(),
			context_object_name: $this->getNumber(),
			context_object_data: [
				'order_item' => $new_item
			]
		);
		
		$this->updated( $change );
		
		return $change;
	}
	
	public function addDiscount( Discounts_Discount $discount ) : Order_ChangeHistory
	{
		/**
		 * @var Order $this
		 */
		
		
		$new_item = new Order_Item();
		$new_item->setupDiscount( $this->getPricelist(), $discount );
		
		$this->addItem( $new_item );
		$this->recalculate();
		$this->save();
		
		$change = $this->startChange();
		$change->addChange('discount_added', '', $new_item->getNumberOfUnits().'x '.$new_item->getTitle() );
		$change->save();
		
		Logger::info(
			event: 'order_item_added:discount',
			event_message: 'Order ('.$this->getNumber().') item:discount has been added',
			context_object_id: $this->getId(),
			context_object_name: $this->getNumber(),
			context_object_data: [
				'order_item' => $new_item
			]
		);
		
		$this->updated( $change );
		
		return $change;
	}

	public function updateEmailAndPhone( ?string $new_email=null, ?string $new_phone=null, bool $update_customer_account=true ) : Order_ChangeHistory
	{
		if($new_email===null) {
			$new_email = $this->getEmail();
		}
		if($new_phone===null) {
			$new_phone = $this->getPhone();
		}
		
		$change = $this->startChange();
		$email_updated = false;
		
		if($new_email!=$this->getEmail()) {
			
			$change->addChange('email', $this->getEmail(), $new_email);
			$this->setEmail( $new_email );
			$email_updated = true;
		}
		
		if($new_phone!=$this->getPhone()) {
			
			$change->addChange('phone', $this->getPhone(), $new_phone);
			$this->setPhone( $new_phone );
			
		}
		
		if($change->hasChange()) {
			$this->save();
			$change->save();
			$this->updated( $change );
			
			$customer = Customer::get( $this->getCustomerId() );
			
			if($customer) {
				if($customer->getEmail()!=$new_email) {
					$customer->changeEmail( $new_email, 'order:'.$this->getNumber() );
				}
				
				if($customer->getPhoneNumber()!=$new_phone) {
					$customer->setPhoneNumber( $new_phone );
				}
				
				$customer->save();
			}
			
		}
		
		return $change;
	}
	
	
	public function changeDeliveryMethod( Delivery_Method $new_delivery_method, string $personal_takeover_delivery_point_code, float $price ) : Order_ChangeHistory
	{
		$change = $this->startChange();
		
		$new_delivery_method->setPrice( $this->getPricelist(), $price );
		
		if( $this->delivery_method_id != $new_delivery_method->getId() ) {
			$change->addChange(
				'delivery_method',
				$this->delivery_method_id,
				$new_delivery_method->getId()
			);
			$this->delivery_method_id = $new_delivery_method->getId();
		}
		
		if($this->delivery_personal_takeover_delivery_point_code!=$personal_takeover_delivery_point_code) {
			$change->addChange(
				'delivery_personal_takeover_delivery_point_code',
				$this->delivery_personal_takeover_delivery_point_code,
				$personal_takeover_delivery_point_code
			);
			
			$this->delivery_personal_takeover_delivery_point_code = $personal_takeover_delivery_point_code;
			
			if( $this->delivery_personal_takeover_delivery_point_code ) {
				$place = $new_delivery_method->getPersonalTakeoverDeliveryPoint( $personal_takeover_delivery_point_code );
				if($place) {
					$billing_address = $this->getBillingAddress();
					
					$delivery_address = new EShopEntity_Address();
					$delivery_address->setFirstName( $billing_address->getFirstName() );
					$delivery_address->setSurname( $billing_address->getSurname() );
					$delivery_address->setCompanyName( $place->getName() );
					$delivery_address->setAddressStreetNo( $place->getStreet() );
					$delivery_address->setAddressTown( $place->getTown() );
					$delivery_address->setAddressZip( $place->getZip() );
					$delivery_address->setAddressCountry( $place->getCountry() );
					
					$this->updateDeliveryAddress( $delivery_address );
					
				}
			}
			
		}
		
		if( $this->delivery_amount_with_VAT!=$price ) {
			$change->addChange(
				'delivery_amount',
				$this->delivery_amount_with_VAT,
				$price
			);
			$this->delivery_amount_with_VAT = $price;
		}
		
		
		if($change->hasChange()) {
			foreach($this->getItems() as $item) {
				if($item->getType()==Order_Item::ITEM_TYPE_DELIVERY) {
					/**
					 * @var Order $this
					 */
					$item->setupDeliveryMethod( $this->getPricelist(), $new_delivery_method );
					$item->save();
				}
			}
			
			$this->recalculate();
			$this->save();
			$change->save();
			
			$this->updated( $change );
		}
		
		
		
		return $change;
	}
	
	public function changePaymentMethod( Payment_Method $new_payment_method, string $payment_method_specification, float $price ) : Order_ChangeHistory
	{
		$change = $this->startChange();
		
		$new_payment_method->getPriceEntity( $this->getPricelist() )->setPrice( $price );
		
		if( $this->payment_method_id != $new_payment_method->getId() ) {
			$change->addChange(
				'payment_method',
				$this->payment_method_id,
				$new_payment_method->getId()
			);
			$this->payment_method_id = $new_payment_method->getId();
		}
		
		if($this->payment_method_specification!=$payment_method_specification) {
			$change->addChange(
				'payment_method_specification',
				$this->payment_method_specification,
				$payment_method_specification
			);
			
			$this->payment_method_specification = $payment_method_specification;
			
		}
		
		if( $this->payment_amount_with_VAT!=$price ) {
			$change->addChange(
				'payment_amount',
				$this->payment_amount_with_VAT,
				$price
			);
			$this->payment_amount_with_VAT = $price;
		}
		
		
		if($change->hasChange()) {
			foreach($this->getItems() as $item) {
				if($item->getType()==Order_Item::ITEM_TYPE_PAYMENT) {
					/**
					 * @var Order $this
					 */
					$item->setupPaymentMethod( $this->getPricelist(), $new_payment_method );
					$item->save();
				}
			}
			
			$this->setPaymentRequired(
				!$new_payment_method->getKind()->isCOD()
			);
			
			$this->recalculate();
			$this->save();
			$change->save();
			
			$this->updated( $change );
		}
		
		
		
		return $change;
	}
	
	public function updateBillingAddress( EShopEntity_Address $address ) : Order_ChangeHistory
	{
		$map = [
			'billing_company_name'      => 'CompanyName',
			'billing_company_id'        => 'CompanyId',
			'billing_company_vat_id'    => 'CompanyVatId',
			'billing_first_name'        => 'FirstName',
			'billing_surname'           => 'Surname',
			'billing_address_street_no' => 'AddressStreetNo',
			'billing_address_town'      => 'AddressTown',
			'billing_address_zip'       => 'AddressZip',
			'billing_address_country'   => 'AddressCountry',
		];
		
		$change = $this->startChange();
		foreach($map as $property=>$gs) {
			
			$address_getter = 'get'.$gs;
			$order_getter = 'getBilling'.$gs;
			$order_setter = 'setBilling'.$gs;
			
			if($this->{$order_getter}() != $address->{$address_getter}()) {
				$change->addChange(
					$property,
					$this->{$order_getter}(),
					$address->{$address_getter}()
				);
				$this->{$order_setter}( $address->{$address_getter}() );
			}
		}
		
		if($change->hasChange()) {
			$change->save();
			$this->save();
			$this->updated( $change );
		}
		
		return $change;
	}
	
	public function updateDeliveryAddress( EShopEntity_Address $address ) : Order_ChangeHistory
	{
		$map = [
			'delivery_company_name'      => 'CompanyName',
			//'delivery_company_id'        => 'CompanyId',
			//'delivery_company_vat_id'    => 'CompanyVatId',
			'delivery_first_name'        => 'FirstName',
			'delivery_surname'           => 'Surname',
			'delivery_address_street_no' => 'AddressStreetNo',
			'delivery_address_town'      => 'AddressTown',
			'delivery_address_zip'       => 'AddressZip',
			'delivery_address_country'   => 'AddressCountry',
		];
		
		$change = $this->startChange();
		foreach($map as $property=>$gs) {
			
			$address_getter = 'get'.$gs;
			$order_getter = 'getDelivery'.$gs;
			$order_setter = 'setDelivery'.$gs;
			
			if($this->{$order_getter}() != $address->{$address_getter}()) {
				$change->addChange(
					$property,
					$this->{$order_getter}(),
					$address->{$address_getter}()
				);
				$this->{$order_setter}( $address->{$address_getter}() );
			}
		}
		
		if($change->hasChange()) {
			$change->save();
			$this->save();
			$this->updated( $change );
		}
		
		return $change;
	}
	
	public function split( array $split_items ) : Order_ChangeHistory
	{
		$new_order = $this->basicClone();
		$new_order->setSplitSourceOrderId( $this->getId() );
		
		
		foreach($this->getItems() as $item) {
			if(!isset($split_items[$item->getId()])) {
				continue;
			}
			
			$new_item = $item->clone();
			$new_item->setNumberOfUnits( $split_items[$item->getId()], $item->getMeasureUnit() );
			$new_order->addItem( $new_item );
		}
		
		$new_order->recalculate();
		$new_order->save();
		
		
		
		
		
		$change = $this->startChange();
		$change->addChange(
			'split_new_order',
			'',
			$new_order->getId()
		);
		
		
		
		foreach($this->getItems() as $item) {
			if(!isset($split_items[$item->getId()])) {
				continue;
			}
			
			$new_qty = $split_items[$item->getId()];
			if(!$new_qty) {
				continue;
			}
			
			$new_qty = $item->getNumberOfUnits() - $new_qty;
			
			
			if(!$new_qty) {
				$change->addChange(
					property: 'idem_deleted',
					old_value:
					$item->getTitle().' ('.$item->getNumberOfUnits().' '.$item->getMeasureUnit()?->getName().' '.Application_Service_Admin::PriceFormatter()->formatWithCurrency($this->getPricelist(), $item->getPricePerUnit()).')',
					new_value: ''
				);
				
				$item->delete();
				unset( $this->items[$item->getId()] );

				
			} else {
				$old = $item->getTitle().': '.$item->getNumberOfUnits().' '.$item->getMeasureUnit()?->getName();
				$item->setNumberOfUnits( $new_qty, $item->getMeasureUnit() );
				$new = $item->getTitle().': '.$item->getNumberOfUnits().' '.$item->getMeasureUnit()?->getName();
				
				$change->addChange(
					property: 'item_number_of_units_updated',
					old_value: $old,
					new_value: $new
				);
				$change->save();
				
				
				$item->save();
			}
			
		}
		
		$this->recalculate();
		$this->save();
		$change->save();
		
		
		$this->updated( $change );
		$new_order->newOrder();
		
		
		return $change;
	}
	
	public function join( Order $join_order ) : bool
	{
		if(
			!$join_order->isEditable() ||
			$join_order->getId()==$this->getId()
		) {
			return false;
		}
		
		$change = $this->startChange();
		
		$change->addChange('order_joined', $join_order->getId(), '');
		
		foreach( $join_order->getItems() as $join_item ) {
			if(
				$join_item->getType()==Order_Item::ITEM_TYPE_PAYMENT ||
				$join_item->getType()==Order_Item::ITEM_TYPE_DELIVERY
			) {
				continue;
			}
			
			foreach($this->getItems() as $current_item) {
				if(
					$join_item->getType()==$current_item->getType() &&
					$join_item->getItemId()==$current_item->getItemId() &&
					$join_item->getItemCode()==$current_item->getItemCode() &&
					$join_item->getSubType()==$current_item->getSubType() &&
					$join_item->getSubCode()==$current_item->getSubCode()
				) {
					$new_number_of_units = $current_item->getNumberOfUnits()+$join_item->getNumberOfUnits();
					
					$old = $current_item->getTitle().': '.$current_item->getNumberOfUnits().' '.$current_item->getMeasureUnit()?->getName();
					$current_item->setNumberOfUnits( $new_number_of_units, $current_item->getMeasureUnit() );
					$new = $current_item->getTitle().': '.$current_item->getNumberOfUnits().' '.$current_item->getMeasureUnit()?->getName();
					
					$change->addChange(
						property: 'item_number_of_units_updated',
						old_value: $old,
						new_value: $new
					);
					
					
					continue 2;
				}
			}
			
			
			$new_item = $join_item->clone();
			$this->addItem( $new_item );
			
			switch($new_item->getType()) {
				case Order_Item::ITEM_TYPE_PRODUCT:
				case Order_Item::ITEM_TYPE_VIRTUAL_PRODUCT:
					$change->addChange('product_added', '', $new_item->getNumberOfUnits().' '.$new_item->getMeasureUnit()?->getName().' '.$new_item->getTitle() );
					break;
				
				case Order_Item::ITEM_TYPE_GIFT:
				case Order_Item::ITEM_TYPE_VIRTUAL_GIFT:
					$change->addChange('gift_added', '', $new_item->getNumberOfUnits().' '.$new_item->getMeasureUnit()?->getName().' '.$new_item->getTitle() );
					break;
				
				case Order_Item::ITEM_TYPE_SERVICE:
					$change->addChange('service_added', '', $new_item->getNumberOfUnits().' '.$new_item->getMeasureUnit()?->getName().' '.$new_item->getTitle() );
					break;
				case Order_Item::ITEM_TYPE_DISCOUNT:
					$change->addChange('discount_added', '', $new_item->getNumberOfUnits().' '.$new_item->getMeasureUnit()?->getName().' '.$new_item->getTitle() );
					break;

			}
			
			
			
		}
		
		$this->recalculate();
		$this->save();
		$change->save();
		$this->updated( $change );
		
		$join_order->setJoinedWithOrderId( $this->getId() );
		$join_order->save();
		
		$join_order->cancel('');

		return true;
	}
	
}
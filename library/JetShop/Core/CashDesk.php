<?php
namespace JetShop;


use JetApplication\Customer_Address;
use JetApplication\Delivery_Method_ShopData;
use JetApplication\Delivery_PersonalTakeover_Place;
use JetApplication\Discounts_Discount;
use JetApplication\Order;
use JetApplication\Payment_Method_Option_ShopData;
use JetApplication\Payment_Method_ShopData;
use JetApplication\Shops_Shop;
use JetApplication\CashDesk_Confirm_AgreeFlag;

interface Core_CashDesk {
	
	public function getShop() : Shops_Shop;
	
	/**
	 * @return Delivery_Method_ShopData[]
	 */
	public function getAvailableDeliveryMethods() : array;
	
	public function getDeliveryMethod( int $id ) : ?Delivery_Method_ShopData;
	
	public function getDefaultDeliveryMethod() : ?Delivery_Method_ShopData;
	
	public function getSelectedDeliveryMethod() : ?Delivery_Method_ShopData;
	
	public function selectDeliveryMethod( int $id ) : bool;
	
	public function selectPersonalTakeoverPlace( int $method_id, string $place_code ) : bool;
	
	public function getSelectedPersonalTakeoverPlace() : ?Delivery_PersonalTakeover_Place;
	
	
	/**
	 * @return Payment_Method_ShopData[]
	 */
	public function getAvailablePaymentMethods() : iterable;
	
	public function getDefaultPaymentMethod() : ?Payment_Method_ShopData;
	
	public function getSelectedPaymentMethod() : Payment_Method_ShopData;
	
	public function selectPaymentMethod( int $id ) : bool;
	
	public function getSelectedPaymentMethodOption() : ?Payment_Method_Option_ShopData;
	
	public function selectPaymentMethodOption( string $option_code ) : bool;
	
	
	public function getEmailAddress() : string;
	
	public function setEmailAddress( string $email ) : void;
	
	
	public function setPassword( string $password ) : void;
	
	public function setNoRegistration( bool $state ) : void;
	
	public function getNoRegistration() : bool;
	
	public function getBillingAddress() : Customer_Address;
	
	public function setBillingAddress( Customer_Address $address ) : void;
	
	public function isCompanyOrder() : bool;
	
	public function setIsCompanyOrder( bool $state ) : void;
	
	public function getPhone() : string;
	
	public function setPhone( string $phone ) : void;
	
	public function hasDifferentDeliveryAddress() : bool;
	
	public function setHasDifferentDeliveryAddress( bool $state ) : void;
	
	public function getDeliveryAddress() : ?Customer_Address;
	
	public function setDeliveryAddress( Customer_Address $address ) : void;
	
	public function onCustomerLogin() : void;
	
	public function onCustomerLogout() : void;
	
	
	
	public function addAgreeFlag( CashDesk_Confirm_AgreeFlag $agree_flag ) : void;
	
	public function removeAgreeFlag( string $code ) : void;
	
	public function getAgreeFlag( string $code ) : ?CashDesk_Confirm_AgreeFlag;
	
	public function getAgreeFlagChecked( string $code ) : bool;
	
	/**
	 * @return CashDesk_Confirm_AgreeFlag[]
	 */
	public function getAgreeFlags() : array;
	
	public function setAgreeFlagState( string $code, bool $state ) : void;
	
	
	public function getSpecialRequirements() : string;
	
	public function setSpecialRequirements( string $comment ) : void;
	
	
	
	
	
	
	public function isReady() : bool;
	
	public function isDone() : bool;
	
	
	public function getCurrentStep(): string;
	
	public function setCurrentStep( string $step ): void;
	
	public function getEmailHasBeenSet() : bool;
	
	public function setEmailHasBeenSet( bool $state ) : void;
	
	
	public function getPhoneHasBeenSet() : bool;
	
	public function setPhoneHasBeenSet( bool $state ) : void;
	
	
	public function getCustomerRegisterOrNotBeenSet() : bool;
	
	public function setCustomerRegisterOrNotBeenSet( bool $state ) : void;
	
	public function getLoyaltyProgramSet() : bool;
	
	public function setLoyaltyProgramSet( bool $state ) : void;
	
	public function getBillingAddressHasBeenSet() : bool;
	
	public function setBillingAddressHasBeenSet( bool $state ) : void;
	
	public function getDifferentDeliveryAddressHasBeenSet() : bool;
	
	public function setDifferentDeliveryAddressHasBeenSet( bool $state ) : void;
	
	public function getDeliveryAddressHasBeenSet() : bool;
	
	public function setDeliveryAddressHasBeenSet( bool $state ) : void;
	
	
	public function isBillingAddressEditable() : bool;
	
	public function setBillingAddressEditable( bool $state ) : void;
	
	public function isDeliveryAddressEditable() : bool;
	
	public function setDeliveryAddressEditable( bool $state ) : void;
	
	public function isDeliveryAddressDisabled() : bool;
	
	
	/**
	 * @return Discounts_Discount[]
	 */
	public function getDiscounts() : array;
	
	public function addDiscount( Discounts_Discount $discount ) : void;
	
	public function removeDiscount( Discounts_Discount $discount ) : void;
	
	
	public function getOrder() : Order;
	
	public function saveOrder() : ?Order;
	
	public function afterOrderSave( Order $order ) : void;
	
}
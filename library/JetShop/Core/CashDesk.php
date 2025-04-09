<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;



use JetApplication\Availability;
use JetApplication\Customer_Address;
use JetApplication\Delivery_Method;
use JetApplication\Carrier_DeliveryPoint;
use JetApplication\Discounts_Discount;
use JetApplication\Order;
use JetApplication\Payment_Method_Option;
use JetApplication\Payment_Method;
use JetApplication\Pricelist;
use JetApplication\EShop;
use JetApplication\CashDesk_Confirm_AgreeFlag;

interface Core_CashDesk {
	
	public function getEshop() : EShop;
	
	public function getPricelist() : Pricelist;
	
	public function getAvailability() : Availability;
	
	/**
	 * @return Delivery_Method[]
	 */
	public function getAvailableDeliveryMethods() : array;
	
	public function getDeliveryMethod( int $id ) : ?Delivery_Method;
	
	public function getDefaultDeliveryMethod() : ?Delivery_Method;
	
	public function getSelectedDeliveryMethod() : ?Delivery_Method;
	
	public function selectDeliveryMethod( int $id ) : bool;
	
	public function selectPersonalTakeoverDeliveryPoint( Delivery_Method $method, Carrier_DeliveryPoint $point ) : bool;
	
	public function getSelectedPersonalTakeoverDeliveryPoint() : ?Carrier_DeliveryPoint;
	
	
	/**
	 * @return Payment_Method[]
	 */
	public function getAvailablePaymentMethods() : iterable;
	
	public function getDefaultPaymentMethod() : ?Payment_Method;
	
	public function getSelectedPaymentMethod() : Payment_Method;
	
	public function selectPaymentMethod( int $id ) : bool;
	
	public function getSelectedPaymentMethodOption() : ?Payment_Method_Option;
	
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
	public function getDiscounts( bool $reset=false ) : array;
	
	public function addDiscount( Discounts_Discount $discount ) : void;
	
	public function removeDiscount( Discounts_Discount $discount ) : void;
	
	
	public function getOrder() : Order;
	
	public function saveOrder() : ?Order;
	
	public function reset() : void;
}
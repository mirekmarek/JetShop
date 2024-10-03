<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Payment\GoPay;

use Jet\Http_Headers;
use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
use JetApplication\Order;
use JetApplication\Payment_Method_Module;
use JetApplication\Payment_Method_ShopData;
use JetApplication\Shop_Pages;

class Main extends Payment_Method_Module implements
	ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface,
	Admin_ControlCentre_Module_Interface
{
	use ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
	use Admin_ControlCentre_Module_Trait;
	
	public function handlePayment( Order $order, Payment_Method_ShopData $payment_method ): bool
	{
		/**
		 * @var Config_PerShop $config
		 */
		$config = $this->getShopConfig( $order->getShop() );

		
		$gopay = new GoPay( $config->getGoPayConfig() );
		$gopay->setLogger( new Logger() );
		
		$payment_id = PaymentPair::getPaymentId( $order->getId() );
		
		if($payment_id) {
			if( $gopay->verifyPayment( $payment_id ) ) {
				return true;
			}
			
			return false;
		}
		
		$o = new GoPay_Order();
		$o->setOderNumber( $order->getNumber() );
		$o->setAmount( $order->getTotalAmount() );
		$o->setDescription( '' );
		$o->setFirstName( $order->getBillingFirstName() );
		$o->setLastName( $order->getBillingSurname() );
		$o->setEmail( $order->getEmail() );
		$o->setPhoneNumber( $order->getPhone() );
		$o->setCity( $order->getBillingAddressTown() );
		$o->setStreet( $order->getBillingAddressStreetNo() );
		$o->setPostalCode( $order->getBillingAddressZip() );
		$o->setCountryCode( $order->getBillingAddressCountry() );

		foreach($order->getItems() as $item) {
			$o_item = new GoPay_Order_Item();
			$o_item->setName( $item->getDescription() );
			$o_item->setCount( $item->getNumberOfUnits() );
			$o_item->setAmount( $item->getTotalAmount() );
		}
		
		$return_url = Shop_Pages::CashDeskPayment()->getURL([$order->getKey()]);
		$notification_url = '';
		
		$selected_bank = GoPay_Bank::get( $order->getPaymentMethodSpecification() );
		
		$gopay_pm = null;
		foreach( GoPay_PaymentMethod::cases() as $_gopay_pm ) {
			if( $_gopay_pm->value == $payment_method->getBackendModulePaymentMethodSpecification() ) {
				$gopay_pm = $_gopay_pm;
				break;
			}
		}
		
		
		$payment = $gopay->createPayment(
			order: $o,
			return_url: $return_url ,
			notification_url: $notification_url,
			payment_method: $gopay_pm,
			selected_bank: $selected_bank
		);
		
		
		if($payment) {
			PaymentPair::setPaymentId(
				$order->getId(),
				$payment->getPaymentId()
			);
			
			Http_Headers::movedTemporary( $payment->getURL() );
			return true;
		}
		
		
		return false;
	}
	
	
	public function getPaymentMethodSpecificationList(): array
	{
		$options = [
			GoPay_PaymentMethod::PAYMENT_CARD->value,
			GoPay_PaymentMethod::BANK_ACCOUNT->value,
			GoPay_PaymentMethod::GOPAY->value,
			GoPay_PaymentMethod::GPAY->value,
			GoPay_PaymentMethod::PRSMS->value,
			GoPay_PaymentMethod::MPAYMENT->value,
			GoPay_PaymentMethod::PAYSAFECARD->value,
			GoPay_PaymentMethod::SUPERCASH->value,
			GoPay_PaymentMethod::PAYPAL->value,
			GoPay_PaymentMethod::BITCOIN->value,
		];
		
		return array_combine( $options, $options );
	}
	
	public function getControlCentreGroup(): string
	{
		return Admin_ControlCentre::GROUP_PAYMENT;
	}
	
	
	public function getControlCentreTitle(): string
	{
		return 'GoPay on-line payment';
	}
	
	public function getControlCentreIcon(): string
	{
		return 'credit-card';
	}
	
	public function getControlCentrePriority(): int
	{
		return 99;
	}
	
	public function getControlCentrePerShopMode(): bool
	{
		return true;
	}
}
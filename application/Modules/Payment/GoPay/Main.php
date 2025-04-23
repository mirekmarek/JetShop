<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Payment\GoPay;


use Jet\Http_Headers;
use Jet\Tr;
use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
use JetApplication\EShops;
use JetApplication\Order;
use JetApplication\Payment_Method;
use JetApplication\Payment_Method_Module;
use JetApplication\SysServices_Definition;
use JetApplication\SysServices_Provider_Interface;

class Main extends Payment_Method_Module implements
	EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface,
	Admin_ControlCentre_Module_Interface,
	SysServices_Provider_Interface
{
	use EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
	use Admin_ControlCentre_Module_Trait;
	
	public function getPaymentMethodOptionsList( Payment_Method $payment_method ) : array
	{
		if( $payment_method->getBackendModulePaymentMethodSpecification()!=GoPay_PaymentMethod::BANK_ACCOUNT ) {
			return [];
		}
		
		return GoPay_Bank::getList();
	}
	
	
	protected function startPayment( Order $order, string $return_url ) : bool
	{
		$config = $this->getEshopConfig( $order->getEshop() );
		
		$gopay = new GoPay( $config->getGoPayConfig() );
		$gopay->setLogger( new Logger() );
		
		$o = new GoPay_Order();
		$o->setOderNumber( $order->getNumber() );
		$o->setAmount( $order->getTotalAmount_WithVAT() );
		$o->setCurrency( $order->getCurrencyCode() );
		$o->setLanguage( strtoupper( $order->getEshop()->getLocale()->getLanguage() ) );
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
		
		$notification_url = '';
		
		$gopay_pm =  $order->getPaymentMethod()->getBackendModulePaymentMethodSpecification();
		$selected_bank = $order->getPaymentMethodSpecification();
		
		$payment = $gopay->createPayment(
			order: $o,
			return_url: $return_url ,
			notification_url: $notification_url,
			payment_method: $gopay_pm,
			selected_bank: $selected_bank
		);
		
		
		if(!$payment) {
			return false;
		}
		
		PaymentPair::setPaymentId(
			$order->getId(),
			$payment->getPaymentId()
		);
		
		Http_Headers::movedTemporary( $payment->getURL() );
		
		return true;
	}
	
	public function handlePayment( Order $order, string $return_url ): bool
	{
		return $this->startPayment( $order, $return_url );
		
	}
	
	public function tryAgain( Order $order, string $return_url ): bool
	{
		return $this->startPayment( $order, $return_url );
		
	}
	
	public function handlePaymentReturn( Order $order ): bool
	{
		if($order->getPaid()) {
			return true;
		}
		
		
		/**
		 * @var Config_PerShop $config
		 */
		$config = $this->getEshopConfig( $order->getEshop() );
		
		$gopay = new GoPay( $config->getGoPayConfig() );
		$gopay->setLogger( new Logger() );
		
		$payments = PaymentPair::getPayments( $order->getId() );
		
		
		foreach($payments as $payment ) {
			if( $gopay->verifyPayment( $payment ) ) {
				$order->paid();
				return true;
			}
		}
		
		return true;
	}
	
	
	
	public function getPaymentMethodSpecificationList(): array
	{
		return GoPay_PaymentMethod::getList();
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
	
	public function getSysServicesDefinitions(): array
	{
		$check_payments = new SysServices_Definition(
			module:        $this,
			name:          Tr::_( 'GoPay - check payments' ),
			description:   Tr::_( 'Verify that all payments have been processed.' ),
			service_code: 'go_pay_check_payments',
			service:       function() {
				/**
				 * @var Config_PerShop $config
				 */
				$config = $this->getEshopConfig( EShops::getCurrent() );
				
				$gopay = new GoPay( $config->getGoPayConfig() );
				$gopay->setLogger( new Logger() );

				$gopay->checkPayments();
			}
		);
		$check_payments->setIsPeriodicallyTriggeredService( true );
		$check_payments->setServiceRequiresEshopDesignation( true );
		
		return [ $check_payments ];
	}
}
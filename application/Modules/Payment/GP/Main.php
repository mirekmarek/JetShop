<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Payment\GP;


use Jet\Tr;
use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
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
		return [];
	}
	
	public function getPaymentMethodSpecificationList(): array
	{
		return GPWebPay_PaymentMethod::getList();
	}
	
	protected function startPayment( Order $order, string $return_url ) : bool
	{
		/**
		 * @var Config_PerShop $config
		 */
		$config = $this->getEshopConfig( $order->getEshop() );
		
		$cl = new GPWebPay( $config );
		
		$cl->process( $order, $return_url );
		
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
		
		$cl = new GPWebPay( $config );
		
		if($cl->verifyPayment( $order )) {
			$order->paid();
			
			return true;
		}

		return false;
	}
	
	
	
	
	public function getControlCentreGroup(): string
	{
		return Admin_ControlCentre::GROUP_PAYMENT;
	}
	
	
	public function getControlCentreTitle(): string
	{
		return 'GP Web Pay on-line payment';
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
			name:          Tr::_( 'GP Web Pay - check payments' ),
			description:   Tr::_( 'Verify that all payments have been processed.' ),
			service_code: 'go_pay_check_payments',
			service:       function() {
				//var_dump(EShops::getCurrent()->getKey());
				//TODO:
			}
		);
		$check_payments->setIsPeriodicallyTriggeredService( true );
		$check_payments->setServiceRequiresEshopDesignation( true );
		
		return [ $check_payments ];
	}
}
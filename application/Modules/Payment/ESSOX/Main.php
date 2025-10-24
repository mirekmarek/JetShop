<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Payment\ESSOX;

use Jet\Application;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
use JetApplication\Order;
use JetApplication\Payment_Method;
use JetApplication\Payment_Method_Module;
use JetApplication\Payment_Method_Module_HasCalculator;
use JetApplication\Pricelist;
use JetApplication\Pricelists;
use JetApplication\Product_EShopData;
use JetApplication\SysServices_Definition;
use JetApplication\SysServices_Provider_Interface;

class Main extends Payment_Method_Module implements
	EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface,
	Admin_ControlCentre_Module_Interface,
	SysServices_Provider_Interface,
	Payment_Method_Module_HasCalculator
{
	
	use EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
	use Admin_ControlCentre_Module_Trait;
	
	public function getPaymentMethodOptionsList( Payment_Method $payment_method ) : array
	{
		return [];
	}
	
	
	protected function startPayment( Order $order, string $return_url ) : bool
	{
		$client = new Client( $this->getEshopConfig($order->getEshop()) );
		$payment_method = $order->getPaymentMethod();
		
		return $client->sendProposal(
			$order,
			$payment_method->getBackendModulePaymentMethodSpecification()==Client::SERVICE_SPLIT,
			$return_url
		);

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
		return true;
	}
	
	
	
	public function getPaymentMethodSpecificationList(): array
	{
		return [
			Client::SERVICE_STD => Tr::_('Standard'),
			Client::SERVICE_SPLIT => Tr::_('Split payment'),
		];
	}
	
	public function getControlCentreGroup(): string
	{
		return Admin_ControlCentre::GROUP_PAYMENT;
	}
	
	
	public function getControlCentreTitle(): string
	{
		return 'ESSOX';
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
		$check_last = new SysServices_Definition(
			module:        $this,
			name:          Tr::_( 'ESSOX - check last contracts' ),
			description:   Tr::_( '' ),
			service_code: 'essox_check_last',
			service:       function() {
				Contract::checkStatuses();
			}
		);
		$check_last->setIsPeriodicallyTriggeredService( true );
		$check_last->setServiceRequiresEshopDesignation( false );
		
		
		$check_all = new SysServices_Definition(
			module:        $this,
			name:          Tr::_( 'ESSOX - check all contracts' ),
			description:   Tr::_( '' ),
			service_code: 'essox_check_all',
			service:       function() {
				Contract::checkStatusesAll();
			}
		);
		$check_all->setIsPeriodicallyTriggeredService( false );
		$check_all->setServiceRequiresEshopDesignation( false );
		
		return [ $check_last, $check_all ];
	}
	
	public function getCalcURL( Product_EShopData $product, ?Pricelist $pricelist=null  ) : string
	{
		if(Http_Request::GET()->exists('show_essox_calc')) {
			ob_end_clean();
			
			$pricelist = $pricelist ? : Pricelists::getCurrent();
			$client = new Client( $this->getEshopConfig( $product->getEshop() ) );
			
			$URL = $client->getCalcURL( $product->getPrice( $pricelist ) );
			if(!$URL) {
				echo $client->last_error_message;
				die();
			}
			
			Http_Headers::movedTemporary( $URL );
			
			Application::end();
		}
		
		return Http_Request::currentURL(['show_essox_calc'=>1, 'cp'=>uniqid()]);
		
		
	}
	
	public function getCalcDefaultTxt( Product_EShopData $product, ?Pricelist $pricelist=null  ): string
	{
		$pricelist = $pricelist ? : Pricelists::getCurrent();
		$product_price = $product->getPrice( $pricelist );
		return 'Na splátky 1/10: záloha '.round(($product_price /100)*10).',-  splátky 10x '.round($product_price/10).',-';
	}
	
	public function renderCalcJavaScript( Product_EShopData $product, ?Pricelist $pricelist=null  ) : string
	{
		return '';
	}
}
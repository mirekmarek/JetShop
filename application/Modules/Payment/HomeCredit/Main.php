<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Payment\HomeCredit;

use Jet\Application;
use Jet\Factory_MVC;
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
use JetApplication\Product_EShopData;
use JetApplication\SysServices_Definition;
use JetApplication\SysServices_Provider_Interface;
use JetApplication\Pricelists;

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
		/**
		 * @var Config_PerShop $config
		 */
		$config = $this->getEshopConfig($order->getEshop());
		
		$a = new CreditApplication( $config );
		
		$o = $order;
		$b_a = $o->getBillingAddress();
		$d_a = $o->getDeliveryAddress();
		
		$billing_adress = new CreditApplication_Address();
		$billing_adress->setAddressType( 'PERMANENT' );
		$billing_adress->setCity( $b_a->getAddressTown() );
		$billing_adress->setStreetAddress( $b_a->getAddressStreetNo() );
		$billing_adress->setStreetNumber( '' );
		$billing_adress->setZip( $b_a->getAddressZip() );
		
		
		$delivery_adress = new CreditApplication_Address();
		$delivery_adress->setAddressType( 'DELIVERY' );
		$delivery_adress->setCity( $d_a->getAddressTown() );
		$delivery_adress->setStreetAddress( $d_a->getAddressStreetNo() );
		$delivery_adress->setStreetNumber( '' );
		$delivery_adress->setZip( $d_a->getAddressZip() );
		
		
		$a->setCustomerFirstName( $b_a->getFirstName() );
		$a->setCustomerLastName( $b_a->getSurname() );
		$a->setCustomerEmail( $o->getEmail() );
		$a->setCustomerPhone( $o->getPhone() );
		
		$a->addCustomerAddress( $billing_adress );
		$a->addCustomerAddress( $delivery_adress );
		
		$a->setOrderNumber( $o->getId() );
		$a->setOrderVariableSymbols( [$o->getId()] );
		
		//$a->setURLNotificationEndpoint('');
		$a->setURLApprovedRedirect( $return_url );
		$a->setURLRejectedRedirect( $return_url );
		
		foreach( $o->getItems() as $order_item ) {
			
			
			if(
				$order_item->isPhysicalProduct() ||
				$order_item->isVirtualProduct()
			) {
				$item = new CreditApplication_OrderItem();
				
				$product = Product_EShopData::get($order_item->getItemId(), $order->getEshop());
				
				
				$item->setCode( $product->getInternalCode() );
				//$item->setEan( $product->getEan() );
				
				$item->setName( $product->getName() );
				$item->setQuantity( $order_item->getNumberOfUnits() );
				
				$item->setAmount( $order_item->getTotalAmount_WithVat() );
				
				$img = $product->getImage( 0 );
				
				$item->setImage(
					$img->getURL(),
					$img->getImageFileName()
				);
				
				$a->addOrderItem( $item );
			}
			
			if($order_item->isDiscount()) {
				$item = new CreditApplication_OrderItem();
				
				$item->setCode( $order_item->getItemCode() );
				//$item->setEan( $product->getEan() );
				
				$item->setName( $order_item->getTitle() );
				$item->setQuantity( $order_item->getNumberOfUnits() );
				
				$item->setAmount( $order_item->getTotalAmount_WithVat() );
				
				$a->addOrderItem( $item );
			}
			
			
		}
		
		/*
		if( isset( $_SESSION[static::SESSION_KEY] ) ) {
			$loan_data = $_SESSION[static::SESSION_KEY];
			
			$a->setCreditparams(
				$loan_data['preferredMonths'],
				$loan_data['preferredInstallment'],
				$loan_data['preferredDownPayment'],
				$loan_data['productCode'],
				'' //$loan_data['productSetCode']
			);
		}
		*/
		
		$client = new Client( $config );
		
		try {
			$client->logim();
			$id = '';
			$redirect_url = '';
			$client->sendCreditApplication( $a, $id, $redirect_url );
			Http_Headers::movedTemporary($redirect_url);
		} catch( Exception $e ) {
			//TODO: log
			return false;
		}
		

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
		return true;
	}
	
	
	
	public function getPaymentMethodSpecificationList(): array
	{
		return [
			'' => ''
		];
	}
	
	public function getControlCentreGroup(): string
	{
		return Admin_ControlCentre::GROUP_PAYMENT;
	}
	
	
	public function getControlCentreTitle(): string
	{
		return 'Home Credit';
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
			name:          Tr::_( 'Home Credit - check last contracts' ),
			description:   Tr::_( '' ),
			service_code: 'home_credit_check_last',
			service:       function() {
				Contract::checkStatuses();
			}
		);
		$check_last->setIsPeriodicallyTriggeredService( true );
		$check_last->setServiceRequiresEshopDesignation( false );
		
		
		$check_all = new SysServices_Definition(
			module:        $this,
			name:          Tr::_( 'Home Credit - check all contracts' ),
			description:   Tr::_( '' ),
			service_code: 'home_credit_check_all',
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
		$pricelist = $pricelist ? : Pricelists::getCurrent();
		
		if(Http_Request::GET()->exists('show_hc_calc')) {
			ob_end_clean();
			
			$config = $this->getEshopConfig( $product->getEshop() );
			
			$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
			$view->setVar('product', $product);
			$view->setVar('pricelist', $pricelist);
			$view->setVar('config', $config);
			$view->setVar('qty', Http_Request::GET()->getInt('qty', 1));
			
			echo $view->render('calc');
			
			Application::end();
		}
		
		return Http_Request::currentURL(['show_hc_calc'=>1, 'cp'=>uniqid()]);
	}
	
	public function getCalcDefaultTxt( Product_EShopData $product, ?Pricelist $pricelist=null  ): string
	{
		return 'Spočítajte si splátky';
	}
	
	public function renderCalcJavaScript( Product_EShopData $product, ?Pricelist $pricelist=null  ) : string
	{
		$pricelist = $pricelist ? : Pricelists::getCurrent();
		$config = $this->getEshopConfig( $product->getEshop() );
		
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		$view->setVar('product', $product);
		$view->setVar('pricelist', $pricelist);
		$view->setVar('config', $config);
		$view->setVar('calc_url', $this->getCalcURL( $product, $pricelist ) );
		
		return $view->render('calc-js');
	}
	
}
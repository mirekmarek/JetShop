<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Payment\Twisto;

use DateTime;
use Jet\AJAX;
use Jet\Factory_MVC;
use Jet\Http_Request;
use Jet\Session;
use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\CashDesk;
use JetApplication\CashDesk_Confirm_AgreeFlag;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
use JetApplication\Order;
use JetApplication\Order_Item;
use JetApplication\Payment_Method;
use JetApplication\Payment_Method_Module;
use JetApplication\SysServices_Provider_Interface;

use Twisto\Twisto;
use Twisto\Customer as Twisto_Customer;
use Twisto\Item as Twisto_OrderItem;
use Twisto\Address as Twisto_Address;
use Twisto\Order as Twisto_Order;
use Twisto\Invoice as Twisto_Invoice;
use Twisto\Error as Twisto_Error;

require_once 'Twisto.php';

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
	
	
	protected function startPayment( Order $order, string $return_url ) : bool
	{
		
		$twisto_transaction_id = $this->getTransactionId();
		$order_id = $order->getId();
		
		
		$tr = Transaction::newTransaction( $order, $twisto_transaction_id );
		if($tr->getTwistoInvoiceId()) {
			return true;
		}
		
		try {
			/**
			 * @var Config_PerShop $config
			 */
			$config = $this->getEshopConfig($order->getEshop());
			
			$twisto = new Twisto();
			
			$twisto->setApiUrl( $config->getApiUrl() );
			$twisto->setPublicKey( $config->getPublickey() );
			$twisto->setSecretKey( $config->getSecretkey() );
			
			
			$invoice = Twisto_Invoice::create( $twisto, $twisto_transaction_id, $order->getNumber() );
		} catch (Twisto_Error $e) {
			
			$tr->setErrorMessage( $e->getMessage() );
			$tr->save();
			
			return false;
		}
		
		$tr->setTwistoInvoiceId( $invoice->invoice_id );
		$tr->save();
		
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
		return 'Twisto';
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
		return [];
	}
	
	
	
	public function generateCheckPayload( Order $order ) : string
	{
		/**
		 * @var Config_PerShop $config
		 */
		$config = $this->getEshopConfig($order->getEshop());

		
		$order_billing_address = $order->getBillingAddress();
		$order_delivery_address = $order->getDeliveryAddress();
		
		
		$email = $order->getEmail();
		$phone = $order->getPhone();
		
		if($order->isCompanyOrder()) {
			$customer = new Twisto_Customer(
				email: $email,
				name: $order_billing_address->getCompanyName(),
				company_id: $order_billing_address->getCompanyId(),
				vat_id: $order_billing_address->getCompanyVatId()
			);
		} else {
			$customer = new Twisto_Customer(
				email: $email,
				name: $order_billing_address->getFirstName().' '.$order_billing_address->getSurname()
			);
		}
		
		$order_items = [];
		
		foreach( $order->getItems() as $item ) {
			switch($item->getType()) {
				case Order_Item::ITEM_TYPE_PRODUCT:
				case Order_Item::ITEM_TYPE_VIRTUAL_PRODUCT:
						$order_items[] = new Twisto_OrderItem(
							Twisto_OrderItem::TYPE_DEFAULT, // type
							name: $item->getTitle(),
							product_id: $item->getItemId(),
							quantity: $item->getNumberOfUnits(),
							price_vat: $item->getTotalAmount_WithVat(),
							vat: $item->getVatRate(),
							ean_code: null,
							isbn_code: null,
							issn_code: null,
							heureka_category: null
						);
					break;
				case Order_Item::ITEM_TYPE_DELIVERY:
					$order_items[] = new Twisto_OrderItem(
						type: Twisto_OrderItem::TYPE_SHIPMENT,
						name: $item->getTitle(),
						product_id: $item->getItemCode(),
						quantity: $item->getNumberOfUnits(),
						price_vat: $item->getTotalAmount_WithVat(),
						vat: $item->getVatRate()
					);
					break;
				case Order_Item::ITEM_TYPE_PAYMENT:
					break;
				case Order_Item::ITEM_TYPE_DISCOUNT:
					$order_items[] = new Twisto_OrderItem(
						Twisto_OrderItem::TYPE_DISCOUNT,
						name: $item->getTitle(),
						product_id: $item->getItemCode(),
						quantity: $item->getNumberOfUnits(),
						price_vat: $item->getTotalAmount_WithVat(),
						vat: $item->getVatRate()
					);
					break;
			}
			
		}
		
		
		
		
		
		
		$billing_address = new Twisto_Address(
			$order_billing_address->getFirstName().' '.$order_billing_address->getSurname(),
			$order_billing_address->getAddressStreetNo(),
			$order_billing_address->getAddressTown(),
			$order_billing_address->getAddressZip(),
			$order_billing_address->getAddressCountry(),
			$phone
		);
		
		
		if($order->getDifferentDeliveryAddress()) {
			$delivery_address = new Twisto_Address(
				$order_delivery_address->getFirstName().' '.$order_delivery_address->getSurname(),
				$order_delivery_address->getAddressStreetNo(),
				$order_delivery_address->getAddressTown(),
				$order_delivery_address->getAddressZip(),
				$order_delivery_address->getAddressCountry(),
				$phone
			);
		} else {
			$delivery_address = $billing_address;
			
		}
		
		
		$twisto = new Twisto();
		
		$twisto->setApiUrl( $config->getApiUrl() );
		$twisto->setPublicKey( $config->getPublickey() );
		$twisto->setSecretKey( $config->getSecretkey() );
		
		
		$previous_orders = [];
		
		
		$order = new Twisto_Order(
			new DateTime(),
			$billing_address,
			$delivery_address,
			$order->getTotalAmount_WithVAT(),
			$order_items
		);
		
		
		$payload = $twisto->getCheckPayload($customer, $order, $previous_orders);
		
		return $payload;
	}
	
	public function generateCachDeskAgreeFlags( CashDesk $cash_desk ) : void
	{
		$twisto_disagree = new CashDesk_Confirm_AgreeFlag('twisto_disagree',
			'Souhlasím s <a href="https://www.twisto.cz/podminky/" target="_blank">všeobecnými obchodními podmínkami</a> služby Twisto.cz (platba první objednávky do 14 dní od doručení zboží) a se zpracováním osobních údajů pro účely této služby.<br />Podmínkou služby je věk 18+ a převzetí zboží zákazníkem.');
		
		$twisto_disagree->setIsMandatory(true);
		$twisto_disagree->setErrorMessage('Pro použití platební metody Twisto je potřeba souhlasit s podmínkami služby.');
		
		
		$cash_desk->addAgreeFlag( $twisto_disagree );
		
	}
	
	protected function getTransactionId() : string
	{
		$session = new Session('twisto');
		return $session->getValue('transaction_id', '');
	}
	
	protected function setTransactionId( string $id ) : void
	{
		$session = new Session('twisto');
		$session->setValue('transaction_id', $id);
	}
	
	public function renderCachDeskConfirmationHandler( CashDesk $cash_desk ) : string
	{
		$GET = Http_Request::GET();
		if($GET->getString('action')=='set_twisto_transaction_id') {
			$this->setTransactionId( $GET->getString('id') );
			AJAX::snippetResponse('');
		}
		
		$this->setTransactionId('');
		
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		$config = $this->getEshopConfig( $cash_desk->getEshop() );
		
		$view->setVar( 'config', $config);
		$view->setVar( 'cash_desk', $cash_desk);
		$view->setVar( 'payload', $this->generateCheckPayload( $cash_desk->getOrder() ));
		
		return $view->render( 'cachdesk_confirmation_handler' );
	}
	
}
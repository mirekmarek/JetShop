<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;



use Jet\MVC;
use Jet\Tr;
use JetApplication\SMS;
use JetApplication\SMS_Template;
use JetApplication\Order;
use JetApplication\Order_Event;
use JetApplication\Application_Service_EShop;
use JetApplication\EShop;

abstract class Core_Order_SMSTemplate extends SMS_Template {
	
	protected Order $order;
	protected Order_Event $event;
	
	public function getOrder(): Order
	{
		return $this->order;
	}
	
	public function setOrder( Order $order ): void
	{
		$this->order = $order;
	}
	
	public function getEvent(): Order_Event
	{
		return $this->event;
	}
	
	public function setEvent( Order_Event $event ): void
	{
		$this->event = $event;
		$this->setOrder( $event->getOrder() );
	}
	
	public function initTest( EShop $eshop ): void
	{
		$ids = Order::dataFetchCol(
			select: ['id'],
			where: $eshop->getWhere(),
			order_by: '-id',
			limit: 1000
		);
		$id_key = array_rand( $ids, 1 );
		$id = $ids[$id_key];
		
		$this->order = Order::get($id);
	}
	
	public function setupSMS( EShop $eshop, SMS $sms ): void
	{
		$sms->setContext( Order::getEntityType() );
		$sms->setContextId( $this->order->getId() );
		$sms->setContextCustomerId( $this->order->getCustomerId() );
		$sms->setSaveHistoryAfterSend( true );
		$sms->setToPhoneNumber( $this->order->getPhone() );
		
	}
	
	protected function initCommonProperties() : void
	{
		$this->addProperty( 'eshop_url', Tr::_( 'e-shop URL' ) )
			->setPropertyValueCreator( function() : string {
				$shop = $this->order->getEshop();
				return MVC::getBase( $shop->getBaseId() )->getHomepage(  $shop->getLocale())->getURL();
			} );
		
		
		$this->addProperty( 'order_number', Tr::_( 'Order number' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getNumber();
			} );
		
		$this->addProperty( 'purchase_date_time', Tr::_( 'Date and time of purchase' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getEshop()->getLocale()->formatDateAndTime( $this->order->getDatePurchased() );
			} );
		
		$this->addProperty( 'total', Tr::_( 'Order total' ) )
			->setPropertyValueCreator( function() : string {
				return Application_Service_EShop::PriceFormatter()->formatWithCurrency($this->order->getTotalAmount_WithVAT(), $this->order->getPricelist());
			} );
		
		
		
		$this->addCondition('billing_company', Tr::_( 'If billing company' ) )
			->setConditionEvaluator( function() : bool {
				return (bool)$this->order->getBillingAddress()->getCompanyName();
			} );
		
		$this->addProperty( 'billing_addr_company_name', Tr::_( 'Billing address - company name' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getBillingAddress()->getCompanyName();
			});
		
		$this->addProperty( 'billing_addr_company_id', Tr::_( 'Billing address - company ID' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getBillingAddress()->getCompanyId();
			});
		
		$this->addProperty( 'billing_addr_company_vat_id', Tr::_( 'Billing address - company VAT ID' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getBillingAddress()->getCompanyVatId();
			});
		
		$this->addProperty( 'billing_addr_first_name', Tr::_( 'Billing address - firs name' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getBillingAddress()->getFirstName();
			});
		
		$this->addProperty( 'billing_addr_surname', Tr::_( 'Billing address - surname' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getBillingAddress()->getSurname();
			});
		
		$this->addProperty( 'billing_addr_street_no', Tr::_( 'Billing address - street and no' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getBillingAddress()->getAddressStreetNo();
			});
		
		$this->addProperty( 'billing_addr_zip', Tr::_( 'Billing address - ZIP' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getBillingAddress()->getAddressZip();
			});
		
		$this->addProperty( 'billing_addr_town', Tr::_( 'Billing address - town' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getBillingAddress()->getAddressTown();
			});
		
		
		
		$this->addCondition('show_delivery_address', Tr::_( 'Show delivery address?' ) )
			->setConditionEvaluator( function() : bool {
				$delivery_method = $this->order->getDeliveryMethod();
				
				if( $delivery_method->getKind()->isEDelivery() ) {
					return false;
				}
				
				return true;
			} );
		
		
		$this->addCondition('delivery_company', Tr::_( 'If delivery company' ) )
			->setConditionEvaluator( function() : bool {
				return (bool)$this->order->getDeliveryAddress()->getCompanyName();
			} );
		
		$this->addProperty( 'delivery_addr_company_name', Tr::_( 'Delivery address - company name' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getDeliveryAddress()->getCompanyName();
			});
		
		$this->addProperty( 'delivery_addr_company_id', Tr::_( 'Delivery address - company ID' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getDeliveryAddress()->getCompanyId();
			});
		
		$this->addProperty( 'delivery_addr_company_vat_id', Tr::_( 'Delivery address - company VAT ID' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getDeliveryAddress()->getCompanyVatId();
			});
		
		$this->addProperty( 'delivery_addr_first_name', Tr::_( 'Delivery address - firs name' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getDeliveryAddress()->getFirstName();
			});
		
		$this->addProperty( 'delivery_addr_surname', Tr::_( 'Delivery address - surname' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getDeliveryAddress()->getSurname();
			});
		
		$this->addProperty( 'delivery_addr_street_no', Tr::_( 'Delivery address - street and no' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getDeliveryAddress()->getAddressStreetNo();
			});
		
		$this->addProperty( 'delivery_addr_zip', Tr::_( 'Delivery address - ZIP' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getDeliveryAddress()->getAddressZip();
			});
		
		$this->addProperty( 'delivery_addr_town', Tr::_( 'Delivery address - town' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getDeliveryAddress()->getAddressTown();
			});
		
		
		$this->addProperty( 'delivery_method_title', Tr::_( 'Delivery method title' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getDeliveryMethod()->getTitle();
			});
		$this->addProperty( 'delivery_method_info', Tr::_( 'Delivery method informations' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getDeliveryMethod()->generateConfirmationEmailInfoText( $this->order );
			});
		
		$this->addProperty( 'payment_method_title', Tr::_( 'Payment method title' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getPaymentMethod()?->getTitle()??'';
			});
		$this->addProperty( 'payment_method_info', Tr::_( 'Payment method informations' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getPaymentMethod()?->generateConfirmationEmailInfoText( $this->order )??'';
			});
		
		$this->addCondition( 'customers_special_requirements', Tr::_( 'Customer has special requirements' ) )
			->setConditionEvaluator( function() : bool {
				return (bool)$this->order->getSpecialRequirements();
			});
		
		$this->addProperty( 'customers_special_requirements', Tr::_( 'Customers special requirements' ) )
			->setPropertyValueCreator( function() : string {
				return nl2br( $this->order->getSpecialRequirements() );
			});
		
		
	}
	
}
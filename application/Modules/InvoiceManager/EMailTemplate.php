<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\InvoiceManager;


use Jet\Tr;
use JetApplication\EMail;
use JetApplication\EMail_Template;
use JetApplication\EShopEntity_AccountingDocument;
use JetApplication\Order;
use JetApplication\EShop_Managers;
use JetApplication\EShop;

abstract class EMailTemplate extends EMail_Template {
	
	protected null|EShopEntity_AccountingDocument $invoice = null;
	
	public function getInvoice(): ?EShopEntity_AccountingDocument
	{
		return $this->invoice;
	}
	
	public function setInvoice( EShopEntity_AccountingDocument $invoice ): void
	{
		$this->invoice = $invoice;
	}
	
	protected function initCommonFields(): void
	{
		$order_number = $this->addProperty( 'invoice_number', Tr::_( 'Delivery note number' ) );
		$order_number->setPropertyValueCreator( function() : string {
			return $this->invoice->getNumber();
		} );
		
		$order_number = $this->addProperty( 'order_number', Tr::_( 'Order number' ) );
		$order_number->setPropertyValueCreator( function() : string {
			if(!$this->invoice->getOrderId()) {
				return '';
			}
			
			$order = Order::get( $this->invoice->getOrderId() );
			
			return $order?->getNumber()??'';
		} );
		
		
		
		$purchased_date_time_property = $this->addProperty( 'date_of_issue', Tr::_( 'Date of issue:' ) );
		$purchased_date_time_property->setPropertyValueCreator( function() : string {
			return $this->invoice->getEshop()->getLocale()->formatDateAndTime( $this->invoice->getInvoiceDate() );
		} );
		
		$total_property = $this->addProperty( 'total', Tr::_( 'Total:' ) );
		$total_property->setPropertyValueCreator( function() : string {
			if($this->invoice->hasVAT()) {
				return EShop_Managers::PriceFormatter()->formatWithCurrency_WithVAT($this->invoice->getTotal(), $this->invoice->getCurrency());
			} else {
				return EShop_Managers::PriceFormatter()->formatWithCurrency_WithoutVAT($this->invoice->getTotal(), $this->invoice->getCurrency());
			}
		} );
		
	}
	
	
	public function setupEMail( EShop $eshop, EMail $email ) : void
	{
		$email->setContext('order');
		$email->setContextId( $this->invoice->getOrderId() );
		$email->setContextCustomerId( $this->invoice->getCustomerId() );
		$email->setSaveHistoryAfterSend( true );
		$email->setTo( $this->invoice->getCustomerEmail() );
		
		$this->generateAttachment( $eshop, $email );
	}
	
	abstract public function generateAttachment( EShop $eshop, EMail $email ) : void;
	
}
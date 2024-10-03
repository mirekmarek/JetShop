<?php /** @noinspection PhpUndefinedClassInspection */

/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\InvoiceManager;

use Jet\Tr;
use JetApplication\EMail;
use JetApplication\EMail_Template;
use JetApplication\Entity_AccountingDocument;
use JetApplication\Order;
use JetApplication\Shop_Managers;
use JetApplication\Shops_Shop;

abstract class EMailTemplate extends EMail_Template {
	
	protected null|Entity_AccountingDocument $invoice = null;
	
	public function getInvoice(): ?Entity_AccountingDocument
	{
		return $this->invoice;
	}
	
	public function setInvoice( Entity_AccountingDocument $invoice ): void
	{
		$this->invoice = $invoice;
	}
	
	protected function initCommonFields(): void
	{
		$this->setInternalName(Tr::_('Correction Invoice'));
		$this->setInternalNotes('');
		
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
			return $this->invoice->getShop()->getLocale()->formatDateAndTime( $this->invoice->getInvoiceDate() );
		} );
		
		$total_property = $this->addProperty( 'total', Tr::_( 'Total:' ) );
		$total_property->setPropertyValueCreator( function() : string {
			return Shop_Managers::PriceFormatter()->formatWithCurrency($this->invoice->getTotal(), $this->invoice->getCurrency());
		} );
		
	}
	
	
	public function setupEMail( Shops_Shop $shop, EMail $email ) : void
	{
		$email->setContext('order');
		$email->setContextId( $this->invoice->getOrderId() );
		$email->setContextCustomerId( $this->invoice->getCustomerId() );
		$email->setSaveHistoryAfterSend( true );
		$email->setTo( $this->invoice->getCustomerEmail() );
		
		$this->generateAttachment( $shop, $email );
	}
	
	abstract public function generateAttachment( Shops_Shop $shop, EMail $email ) : void;
	
}
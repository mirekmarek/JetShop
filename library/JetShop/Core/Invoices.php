<?php
namespace JetShop;

use Jet\Application_Module;
use JetApplication\DeliveryNote;
use JetApplication\Invoice;
use JetApplication\Invoice_Manager;
use JetApplication\InvoiceInAdvance;
use JetApplication\Managers_General;
use JetApplication\Order;

abstract class Core_Invoices {
	
	public static function getManager() : Invoice_Manager|Application_Module
	{
		return Managers_General::Invoices();
	}
	
	public static function createInvoiceForOrder( Order $order ) : Invoice
	{
		return static::getManager()->createInvoiceForOrder( $order );
	}
	
	public static function createInvoiceInAdvanceForOrder( Order $order ) : InvoiceInAdvance
	{
		return static::getManager()->createInvoiceInAdvanceForOrder( $order );
	}
	
	public static function createDeliveryNoteForOrder( Order $order ) : DeliveryNote
	{
		return static::getManager()->createDeliveryNoteForOrder( $order );
	}
	
	
	public static function getInvoicePDFTemplates() : array
	{
		return static::getManager()->getInvoicePDFTemplates();
	}
	
	public static function getInvoiceInAdvancePDFTemplates() : array
	{
		return static::getManager()->getInvoiceInAdvancePDFTemplates();
	}
	
	public static function getDeliveryNotePDFTemplates() : array
	{
		return static::getManager()->getDeliveryNotePDFTemplates();
	}
	
	
	public static function generateInvoicePDF( Invoice $invoice, string $force_template='' ) : string
	{
		return static::getManager()->generateInvoicePDF( $invoice, $force_template );
	}
	
	public static function generateInvoiceInAdvancePDF( InvoiceInAdvance $invoice, string $force_template='' ) : string
	{
		return static::getManager()->generateInvoiceInAdvancePDF( $invoice, $force_template );
	}
	
	public static function generateDeliveryNotePDF( DeliveryNote $invoice, string $force_template='' ) : string
	{
		return static::getManager()->generateDeliveryNotePDF( $invoice, $force_template );
	}
	
	
	
	
	public static function sendInvoice( Invoice $invoice ) : void
	{
		static::getManager()->sendInvoice( $invoice );
	}
	
	public static function sendInvoiceInAdvance( InvoiceInAdvance $invoice ) : void
	{
		static::getManager()->sendInvoiceInAdvance( $invoice );
	}
	
	public static function sendDeliveryNote( DeliveryNote $invoice ) : void
	{
		static::getManager()->sendDeliveryNote( $invoice );
	}
	
	
}
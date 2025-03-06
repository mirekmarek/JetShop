<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
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
	
	public static function generateInvoicePDF( Invoice $invoice ) : string
	{
		return static::getManager()->generateInvoicePDF( $invoice );
	}
	
	public static function generateInvoiceInAdvancePDF( InvoiceInAdvance $invoice ) : string
	{
		return static::getManager()->generateInvoiceInAdvancePDF( $invoice );
	}
	
	public static function generateDeliveryNotePDF( DeliveryNote $invoice ) : string
	{
		return static::getManager()->generateDeliveryNotePDF( $invoice );
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
<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\DeliveryNote;
use JetApplication\Invoice;
use JetApplication\InvoiceInAdvance;
use JetApplication\Order;

interface Core_Invoice_Manager
{
	public function getInvoicePDFTemplates() : array;
	public function getInvoiceInAdvancePDFTemplates() : array;
	public function getDeliveryNotePDFTemplates() : array;
	
	public function createInvoiceForOrder( Order $order ) : Invoice;
	public function createInvoiceInAdvanceForOrder( Order $order ) : InvoiceInAdvance;
	public function createDeliveryNoteForOrder( Order $order ) : DeliveryNote;
	
	public function generateInvoicePDF( Invoice $invoice, string $force_template=''  ) : string;
	public function generateInvoiceInAdvancePDF( InvoiceInAdvance $invoice, string $force_template=''  ) : string;
	public function generateDeliveryNotePDF( DeliveryNote $invoice, string $force_template=''  ) : string;
	
	public function sendInvoice( Invoice $invoice ) : void;
	public function sendInvoiceInAdvance( InvoiceInAdvance $invoice ) : void;
	public function sendDeliveryNote( DeliveryNote $invoice ) : void;
	
}
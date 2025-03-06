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
use JetApplication\InvoiceInAdvance;
use JetApplication\Manager_MetaInfo;
use JetApplication\Order;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_GENERAL,
	is_mandatory: true,
	name: 'Invoices',
	description: '',
	module_name_prefix: ''
)]
abstract class Core_Invoice_Manager extends Application_Module
{
	
	abstract public function createInvoiceForOrder( Order $order ) : Invoice;
	abstract public function createInvoiceInAdvanceForOrder( Order $order ) : InvoiceInAdvance;
	abstract public function createDeliveryNoteForOrder( Order $order ) : DeliveryNote;
	
	abstract public function generateInvoicePDF( Invoice $invoice  ) : string;
	abstract public function generateInvoiceInAdvancePDF( InvoiceInAdvance $invoice ) : string;
	abstract public function generateDeliveryNotePDF( DeliveryNote $invoice ) : string;
	
	abstract public function sendInvoice( Invoice $invoice ) : void;
	abstract public function sendInvoiceInAdvance( InvoiceInAdvance $invoice ) : void;
	abstract public function sendDeliveryNote( DeliveryNote $invoice ) : void;
	
}
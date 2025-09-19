<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use JetApplication\Application_Service_General;
use JetApplication\DeliveryNote;
use JetApplication\Invoice;
use JetApplication\ProformaInvoice;
use Jet\Application_Service_MetaInfo;
use JetApplication\Order;

#[Application_Service_MetaInfo(
	group: Application_Service_General::GROUP,
	is_mandatory: true,
	name: 'Invoices',
	description: '',
	module_name_prefix: ''
)]
abstract class Core_Application_Service_General_Invoices extends Application_Module
{
	
	abstract public function createInvoiceForOrder( Order $order ) : Invoice;
	abstract public function createProformaInvoiceForOrder( Order $order ) : ProformaInvoice;
	abstract public function createDeliveryNoteForOrder( Order $order ) : DeliveryNote;
	
	abstract public function generateInvoicePDF( Invoice $invoice  ) : string;
	abstract public function generateProformaInvoicePDF( ProformaInvoice $invoice ) : string;
	abstract public function generateDeliveryNotePDF( DeliveryNote $invoice ) : string;
	
	abstract public function sendInvoice( Invoice $invoice ) : void;
	abstract public function sendProformaInvoice( ProformaInvoice $invoice ) : void;
	abstract public function sendDeliveryNote( DeliveryNote $invoice ) : void;
	
}
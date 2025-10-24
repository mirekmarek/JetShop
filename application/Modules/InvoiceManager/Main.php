<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */

/** @noinspection PhpUndefinedClassInspection */
namespace JetApplicationModule\InvoiceManager;

use JetApplication\DeliveryNote;
use JetApplication\EMail_Template;
use JetApplication\EMail_TemplateProvider;
use JetApplication\Invoice;
use JetApplication\Application_Service_General_Invoices;
use JetApplication\ProformaInvoice;
use JetApplication\Order;
use JetApplication\PDF_TemplateProvider;
use TCPDF;


class Main extends Application_Service_General_Invoices implements EMail_TemplateProvider, PDF_TemplateProvider
{
	
	public function createInvoiceForOrder( Order $order ) : Invoice
	{
		$invoice = Invoice::createByOrder( $order );
		//TODO: setup payment, atc.
		
		$invoice->save();
		
		return $invoice;
	}
	
	public function createProformaInvoiceForOrder( Order $order ) : ProformaInvoice
	{
		$invoice = ProformaInvoice::createByOrder( $order );
		//TODO: setup payment, atc.
		
		$invoice->setNumber( $order->getNumber() );
		$invoice->save();
		
		return $invoice;
	}
	
	public function createDeliveryNoteForOrder( Order $order ) : DeliveryNote
	{
		$invoice = DeliveryNote::createByOrder( $order );
		//TODO: setup payment, atc.
		
		$invoice->save();
		
		return $invoice;
	}
	
	public function generateInvoicePDF( Invoice $invoice ) : string
	{
		$template = new PDFTemplate_Invoice();
		$template->setInvoice( $invoice );
		
		return $template->generatePDF( $invoice->getEshop() );
	}
	
	public function generateProformaInvoicePDF( ProformaInvoice $invoice ) : string
	{
		$template = new PDFTemplate_ProformaInvoice();
		$template->setInvoice( $invoice );
		
		return $template->generatePDF( $invoice->getEshop() );
	}
	
	
	public function generateDeliveryNotePDF( DeliveryNote $invoice  ) : string
	{
		$template = new PDFTemplate_DeliveryNote();
		$template->setInvoice( $invoice );
		
		return $template->generatePDF( $invoice->getEshop() );
	}
	
	
	public function sendInvoice( Invoice $invoice ) : void
	{
		$email_template = new EMailTemplate_Invoice();
		$email_template->setInvoice( $invoice );
		
		$email_template->createEmail( $invoice->getEshop() )?->send();
	}
	
	public function sendProformaInvoice( ProformaInvoice $invoice ) : void
	{
		$email_template = new EMailTemplate_ProformaInvoice();
		$email_template->setInvoice( $invoice );
		
		$email_template->createEmail( $invoice->getEshop() )?->send();
	}
	
	public function sendDeliveryNote( DeliveryNote $invoice ) : void
	{
		$email_template = new EMailTemplate_DeliveryNote();
		$email_template->setInvoice( $invoice );
		
		$email_template->createEmail( $invoice->getEshop() )?->send();
	}
	
	
	/**
	 * @return EMail_Template[]
	 */
	public function getEMailTemplates(): array
	{
		$invoice = new EMailTemplate_Invoice();
		$correction_invoice = new EMailTemplate_CorrectionInvoice();
		$proforma_invoice = new EMailTemplate_ProformaInvoice();
		$delivery_note = new EMailTemplate_DeliveryNote();
		
		return [
			$invoice,
			$proforma_invoice,
			$correction_invoice,
			$delivery_note
		];
	}
	
	/**
	 * @return PDF_Template[]
	 */
	public function getPDFTemplates(): array
	{
		$invoice = new PDFTemplate_Invoice();
		$correction_invoice = new PDFTemplate_CorrectionInvoice();
		$proforma_invoice = new PDFTemplate_ProformaInvoice();
		$delivery_note = new PDFTemplate_DeliveryNote();
		
		return [
			$invoice,
			$proforma_invoice,
			$correction_invoice,
			$delivery_note
		];
	}
}
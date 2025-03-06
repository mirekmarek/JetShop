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
use JetApplication\Invoice_Manager;
use JetApplication\InvoiceInAdvance;
use JetApplication\Order;
use JetApplication\PDF_TemplateProvider;
use TCPDF;


class Main extends Invoice_Manager implements EMail_TemplateProvider, PDF_TemplateProvider
{
	
	public function createInvoiceForOrder( Order $order ) : Invoice
	{
		$invoice = Invoice::createByOrder( $order );
		//TODO: setup payment, atc.
		
		$invoice->save();
		
		return $invoice;
	}
	
	public function createInvoiceInAdvanceForOrder( Order $order ) : InvoiceInAdvance
	{
		$invoice = InvoiceInAdvance::createByOrder( $order );
		//TODO: setup payment, atc.
		
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
	
	public function generateInvoiceInAdvancePDF( InvoiceInAdvance $invoice ) : string
	{
		$template = new PDFTemplate_InvoiceInAdvance();
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
		
		$email_template->createEmail( $invoice->getEshop() )->send();
	}
	
	public function sendInvoiceInAdvance( InvoiceInAdvance $invoice ) : void
	{
		$email_template = new EMailTemplate_InvoiceInAdvance();
		$email_template->setInvoice( $invoice );
		
		$email_template->createEmail( $invoice->getEshop() )->send();
	}
	
	public function sendDeliveryNote( DeliveryNote $invoice ) : void
	{
		$email_template = new EMailTemplate_DeliveryNote();
		$email_template->setInvoice( $invoice );
		
		$email_template->createEmail( $invoice->getEshop() )->send();
	}
	
	
	/**
	 * @return EMail_Template[]
	 */
	public function getEMailTemplates(): array
	{
		$invoice = new EMailTemplate_Invoice();
		$correction_invoice = new EMailTemplate_CorrectionInvoice();
		$invoice_in_advance = new EMailTemplate_InvoiceInAdvance();
		$delivery_note = new EMailTemplate_DeliveryNote();
		
		return [
			$invoice,
			$invoice_in_advance,
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
		$invoice_in_advance = new PDFTemplate_InvoiceInAdvance();
		$delivery_note = new PDFTemplate_DeliveryNote();
		
		return [
			$invoice,
			$invoice_in_advance,
			$correction_invoice,
			$delivery_note
		];
	}
}
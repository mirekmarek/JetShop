<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */

/** @noinspection PhpUndefinedClassInspection */
namespace JetApplicaTionModule\InvoiceManager;


use Closure;
use Jet\Application_Module;
use Jet\Factory_MVC;
use Jet\IO_Dir;
use Jet\Locale;
use Jet\Tr;
use JetApplication\CompanyInfo;
use JetApplication\DeliveryNote;
use JetApplication\EMail_Template;
use JetApplication\EMail_TemplateProvider;
use JetApplication\Invoice;
use JetApplication\Invoice_Manager;
use JetApplication\InvoiceInAdvance;
use JetApplication\Order;
use TCPDF;


class Main extends Application_Module implements Invoice_Manager, EMail_TemplateProvider
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
	
	public function generateInvoicePDF( Invoice $invoice, string $force_template=''  ) : string
	{
		if(!$force_template) {
			$force_template = CompanyInfo::get( $invoice->getEshop() )->getInvoicePdfTemplate();
			if(!$force_template) {
				$force_template = 'default';
			}
		}
		
		
		return $this->generatePDF(
			$invoice,
			$force_template,
			'pdf-templates/invoice/'
		);
	}
	
	public function generateInvoiceInAdvancePDF( InvoiceInAdvance $invoice, string $force_template=''  ) : string
	{
		if(!$force_template) {
			$force_template = CompanyInfo::get( $invoice->getEshop() )->getInvoiceInAdvancePdfTemplate();
			if(!$force_template) {
				$force_template = 'default';
			}
		}
		
		
		return $this->generatePDF(
			$invoice,
			$force_template,
			'pdf-templates/invoice-in-advance/'
		);
	}
	
	
	public function generateDeliveryNotePDF( DeliveryNote $invoice, string $force_template=''  ) : string
	{
		if(!$force_template) {
			$force_template = CompanyInfo::get( $invoice->getEshop() )->getInvoiceInAdvancePdfTemplate();
			if(!$force_template) {
				$force_template = 'default';
			}
		}
		
		
		return $this->generatePDF(
			$invoice,
			$force_template,
			'pdf-templates/delivery-note/'
		);
	}
	
	
	
	public function getInvoicePDFTemplates() : array
	{
		return $this->getTemplates( 'pdf-templates/invoice/' );
	}
	
	public function getInvoiceInAdvancePDFTemplates() : array
	{
		return $this->getTemplates( 'pdf-templates/invoice-in-advance/' );
	}
	
	public function getDeliveryNotePDFTemplates() : array
	{
		return $this->getTemplates( 'pdf-templates/delivery-note/' );
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
	
	
	
	
	protected function getTemplates( string $dir ) : array
	{
		$views = IO_Dir::getFilesList( $this->getViewsDir().$dir, '*.phtml' );
		
		$templates = [];
		
		foreach($views as $path=>$name) {
			$name = str_replace( '.phtml', '', $name );
			$templates[$name] = $name;
		}
		
		return $templates;
	}
	
	
	/** @noinspection PhpUndefinedClassInspection
	 * @noinspection PhpMethodParametersCountMismatchInspection
	 */
	protected function generatePDF( Invoice|InvoiceInAdvance|DeliveryNote $invoice, string $force_template, string $template_dir  ) : string
	{
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() use ($invoice, $force_template, $template_dir) {
				$current_locale = Locale::getCurrentLocale();
				Locale::setCurrentLocale( $invoice->getEshop()->getLocale() );
				Tr::setCurrentLocale( $invoice->getEshop()->getLocale() );
				
				$tcpdf = new class(
					'P',
					'mm',
					'A4',
					true,
					'UTF-8'
				) extends TCPDF {
					public Invoice|InvoiceInAdvance|DeliveryNote $invoice;
					public ?Closure $header_generator = null;
					public ?Closure $footer_generator = null;
					
					/** @noinspection PhpMissingReturnTypeInspection */
					public function Header() {
						$this->header_generator?->call( $this, $this, $this->invoice );
					}
					
					/** @noinspection PhpMissingReturnTypeInspection */
					public function Footer()  {
						$this->footer_generator?->call( $this, $this, $this->invoice );
					}
				};
				
				$tcpdf->invoice = $invoice;
				
				$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
				$view->setVar( 'invoice', $invoice );
				$view->setVar( 'tcpdf', $tcpdf );
				
				$html = $view->render( $template_dir.$force_template );
				
				$tcpdf->AddPage();
				
				$tcpdf->writeHTML( $html );
				
				$output = $tcpdf->Output('', 'S');
				
				
				Tr::setCurrentLocale( $current_locale );
				Locale::setCurrentLocale( $current_locale );
				
				return $output;
				
			}
		);
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
}
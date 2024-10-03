<?php
namespace JetApplicationModule\InvoiceManager;

use Jet\Tr;
use JetApplication\EMail;
use JetApplication\Invoice;
use JetApplication\Invoices;
use JetApplication\Shops_Shop;

class EMailTemplate_Invoice extends EMailTemplate {
	
	
	protected function init(): void
	{
		$this->setInternalName(Tr::_('Correction Invoice'));
		$this->setInternalNotes('');
		
		$this->initCommonFields();
	}
	
	
	public function initTest( Shops_Shop $shop ): void
	{
		$ids = Invoice::dataFetchCol(
			select: ['id'],
			where: [$shop->getWhere(), 'AND', ['correction_invoice'=>false]],
			order_by: '-id',
			limit: 1000
		);
		$id_key = array_rand( $ids, 1 );
		$id = $ids[$id_key];
		
		$this->invoice = Invoice::get($id);
	}
	
	public function generateAttachment( Shops_Shop $shop, EMail $email ) : void
	{
		/**
		 * @var Invoice $invoice
		 */
		$invoice = $this->invoice;
		
		$pdf = Invoices::generateInvoicePDF( $invoice );
		
		$email->addAttachmentsData(
			file_name: $this->invoice->getNumber().'.pdf',
			file_mime_type: 'application/pdf',
			file_data: $pdf
		);
	}

}
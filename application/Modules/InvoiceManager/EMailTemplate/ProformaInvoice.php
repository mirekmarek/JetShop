<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\InvoiceManager;


use Jet\Tr;
use JetApplication\EMail;
use JetApplication\ProformaInvoice;
use JetApplication\Invoices;
use JetApplication\EShop;

class EMailTemplate_ProformaInvoice extends EMailTemplate
{
	protected function init(): void
	{
		$this->setInternalName(Tr::_('Proforma Invoice'));
		$this->setInternalNotes('');
		
		$this->initCommonFields();
	}
	
	public function initTest( EShop $eshop ): void
	{
		$ids = ProformaInvoice::dataFetchCol(
			select: ['id'],
			where: $eshop->getWhere(),
			order_by: '-id',
			limit: 1000
		);
		$id_key = array_rand( $ids, 1 );
		$id = $ids[$id_key];
		
		$this->invoice = ProformaInvoice::get($id);
	}
	
	public function generateAttachment( EShop $eshop, EMail $email ): void
	{
		/**
		 * @var ProformaInvoice $invoice
		 */
		$invoice = $this->invoice;
		
		$pdf = Invoices::generateProformaInvoicePDF( $invoice );
		
		$email->addAttachmentsData(
			file_name: $this->invoice->getNumber().'.pdf',
			file_mime_type: 'application/pdf',
			file_data: $pdf
		);

	}
}
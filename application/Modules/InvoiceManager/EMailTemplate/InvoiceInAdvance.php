<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\InvoiceManager;


use Jet\Tr;
use JetApplication\EMail;
use JetApplication\InvoiceInAdvance;
use JetApplication\Invoices;
use JetApplication\EShop;

class EMailTemplate_InvoiceInAdvance extends EMailTemplate
{
	protected function init(): void
	{
		$this->setInternalName(Tr::_('Invoice in advance'));
		$this->setInternalNotes('');
		
		$this->initCommonFields();
	}
	
	public function initTest( EShop $eshop ): void
	{
		$ids = InvoiceInAdvance::dataFetchCol(
			select: ['id'],
			where: $eshop->getWhere(),
			order_by: '-id',
			limit: 1000
		);
		$id_key = array_rand( $ids, 1 );
		$id = $ids[$id_key];
		
		$this->invoice = InvoiceInAdvance::get($id);
	}
	
	public function generateAttachment( EShop $eshop, EMail $email ): void
	{
		/**
		 * @var InvoiceInAdvance $invoice
		 */
		$invoice = $this->invoice;
		
		$pdf = Invoices::generateInvoiceInAdvancePDF( $invoice );
		
		$email->addAttachmentsData(
			file_name: $this->invoice->getNumber().'.pdf',
			file_mime_type: 'application/pdf',
			file_data: $pdf
		);

	}
}
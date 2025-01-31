<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\InvoiceManager;


use Jet\Tr;
use JetApplication\DeliveryNote;
use JetApplication\EMail;
use JetApplication\Invoices;
use JetApplication\EShop;

class EMailTemplate_DeliveryNote extends EMailTemplate
{
	protected function init(): void
	{
		$this->setInternalName(Tr::_('Delivery note'));
		$this->setInternalNotes('');
		
		$this->initCommonFields();
	}
	
	
	public function initTest( EShop $eshop ): void
	{
		$ids = DeliveryNote::dataFetchCol(
			select: ['id'],
			where: $eshop->getWhere(),
			order_by: '-id',
			limit: 1000
		);
		$id_key = array_rand( $ids, 1 );
		$id = $ids[$id_key];

		
		$this->invoice = DeliveryNote::get($id);
	}
	
	public function generateAttachment( EShop $eshop, EMail $email ): void
	{
		/**
		 * @var DeliveryNote $invoice
		 */
		$invoice = $this->invoice;
		
		$pdf = Invoices::generateDeliveryNotePDF( $invoice );
		
		$email->addAttachmentsData(
			file_name: $invoice->getNumber().'.pdf',
			file_mime_type: 'application/pdf',
			file_data: $pdf
		);
	}
}
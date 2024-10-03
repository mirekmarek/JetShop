<?php
namespace JetApplicationModule\InvoiceManager;

use Jet\Tr;
use JetApplication\DeliveryNote;
use JetApplication\EMail;
use JetApplication\Invoices;
use JetApplication\Shops_Shop;

class EMailTemplate_DeliveryNote extends EMailTemplate
{
	protected function init(): void
	{
		$this->setInternalName(Tr::_('Delivery note'));
		$this->setInternalNotes('');
		
		$this->initCommonFields();
	}
	
	
	public function initTest( Shops_Shop $shop ): void
	{
		$ids = DeliveryNote::dataFetchCol(
			select: ['id'],
			where: $shop->getWhere(),
			order_by: '-id',
			limit: 1000
		);
		$id_key = array_rand( $ids, 1 );
		$id = $ids[$id_key];

		
		$this->invoice = DeliveryNote::get($id);
	}
	
	public function generateAttachment( Shops_Shop $shop, EMail $email ): void
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
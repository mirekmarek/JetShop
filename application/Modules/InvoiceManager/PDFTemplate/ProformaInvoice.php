<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\InvoiceManager;


use Jet\Tr;
use JetApplication\EShop;
use JetApplication\ProformaInvoice;
use JetApplication\Template_Property_Param;

class PDFTemplate_ProformaInvoice extends PDFTemplate {
	
	protected function init(): void
	{
		$this->setInternalName(Tr::_('Proforma Invoice'));
		$this->setInternalNotes('');
		
		$this->initCommonFields();
		
		$qr = $this->addProperty('qr', Tr::_('QR'));
		$qr->setPropertyValueCreator(function() : string {
			/**
			 * @var ProformaInvoice $invoice
			 */
			$invoice = $this->invoice;
			return $invoice->getPaymentQrCodeImageFilename();

		});
		$qr->addParam( Template_Property_Param::TYPE_INT, 'max_w', Tr::_('Maximal image width') );
		$qr->addParam( Template_Property_Param::TYPE_INT, 'max_h', Tr::_('Maximal image height') );
		
		$this->addCondition('qr', 'QR code defined')
			->setConditionEvaluator( function() : bool {
				/**
				 * @var ProformaInvoice $invoice
				 */
				$invoice = $this->invoice;
				return (bool)$invoice->getPaymentQrCodeImageFilename();
			});
		
		$this->addProperty('text_before_items', Tr::_('Text before ttems'))
			->setPropertyValueCreator(function() : string {
				/**
				 * @var ProformaInvoice $invoice
				 */
				$invoice = $this->invoice;
				return $invoice->getTextBeforeItems();
			});
		
		$this->addProperty('text_after_items', Tr::_('Text after ttems'))
			->setPropertyValueCreator(function() : string {
				/**
				 * @var ProformaInvoice $invoice
				 */
				$invoice = $this->invoice;
				return $invoice->getTextAftereItems();
			});
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
		
		$this->setInvoice( ProformaInvoice::get($id) );
	}
	
}
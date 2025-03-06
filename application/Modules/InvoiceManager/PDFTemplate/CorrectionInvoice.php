<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\InvoiceManager;


use Jet\Tr;
use JetApplication\EShop;
use JetApplication\Invoice;

class PDFTemplate_CorrectionInvoice extends PDFTemplate {
	
	protected function init(): void
	{
		$this->setInternalName(Tr::_('Correction Invoice'));
		$this->setInternalNotes('');
		
		$this->initCommonFields();
	}
	
	public function initTest( EShop $eshop ): void
	{
		$ids = Invoice::dataFetchCol(
			select: ['id'],
			where: [$eshop->getWhere(), 'AND', ['correction_invoice' =>true]],
			order_by: '-id',
			limit: 1000
		);
		$id_key = array_rand( $ids, 1 );
		$id = $ids[$id_key];
		
		$this->setInvoice( Invoice::get($id) );
	}
	

}
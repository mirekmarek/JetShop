<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\ReceiptOfGoods;

use Jet\Tr;
use JetApplication\Admin_Listing_Column;
use JetApplication\WarehouseManagement_ReceiptOfGoods;

class Listing_Column_ReceiptDate extends Admin_Listing_Column
{
	public const KEY = 'receipt_date';
	
	public function getTitle(): string
	{
		return Tr::_('Receipt Date');
	}
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): object
	{
		/**
		 * @var WarehouseManagement_ReceiptOfGoods $item
		 */
		return $item->getReceiptDate();
	}
}
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

class Listing_Column_OrderNumber extends Admin_Listing_Column
{
	public const KEY = 'order_number';

	public function getTitle(): string
	{
		return Tr::_('Order number');
	}
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): string
	{
		/**
		 * @var WarehouseManagement_ReceiptOfGoods $item
		 */
		return $item->getOrderNumber();
	}
}
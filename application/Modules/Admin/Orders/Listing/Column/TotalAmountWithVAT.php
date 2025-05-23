<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;

use Jet\Tr;
use JetApplication\Admin_Listing_Column;
use JetApplication\Order;

class Listing_Column_TotalAmountWithVAT extends Admin_Listing_Column
{
	public const KEY = 'total_amount_with_VAT';
	
	public function getTitle(): string
	{
		return Tr::_('Total amount');
	}
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): float
	{
		/**
		 * @var Order $item
		 */
		return $item->getTotalAmount_WithVAT();
	}
}
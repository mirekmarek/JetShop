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

class Listing_Column_TotalAmount extends Admin_Listing_Column
{
	public const KEY = 'total_amount';
	
	public function getTitle(): string
	{
		return Tr::_('Amount');
	}
	
	public function getExportHeader(): null|string|array
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): float|int|bool|string|array
	{
		/**
		 * @var Order $item
		 */
		return $item->getTotalAmount_WithVAT();
	}
}
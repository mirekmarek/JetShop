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

class Listing_Column_DatePurchased extends Admin_Listing_Column
{
	public const KEY = 'date_purchased';
	
	public function getTitle(): string
	{
		return Tr::_('Date purchased');
	}
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): object
	{
		/**
		 * @var Order $item
		 */
		return $item->getDatePurchased();
	}
}
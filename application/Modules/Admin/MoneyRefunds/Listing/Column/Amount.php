<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\MoneyRefunds;

use Jet\Tr;
use JetApplication\Admin_Listing_Column;
use JetApplication\MoneyRefund;

class Listing_Column_Amount extends Admin_Listing_Column
{
	public const KEY = 'amount_to_be_refunded';
	
	public function getTitle(): string
	{
		return Tr::_('Amount to be refunded');
	}
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): float
	{
		/**
		 * @var MoneyRefund $item
		 */
		return $item->getAmountToBeRefunded();
	}
}
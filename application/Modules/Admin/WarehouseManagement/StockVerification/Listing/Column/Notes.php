<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\StockVerification;

use Jet\Tr;
use JetApplication\Admin_Listing_Column;
use JetApplication\WarehouseManagement_StockVerification;

class Listing_Column_Notes extends Admin_Listing_Column
{
	public const KEY = 'notes';
	
	public function getTitle(): string
	{
		return Tr::_('Notes');
	}
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getDisallowSort() : bool
	{
		return true;
	}
	
	public function getExportData( mixed $item ): string
	{
		/**
		 * @var WarehouseManagement_StockVerification $item
		 */
		return $item->getNotes();
	}
}
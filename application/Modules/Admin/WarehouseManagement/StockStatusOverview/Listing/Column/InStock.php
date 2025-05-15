<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\StockStatusOverview;

use Jet\Tr;
use JetApplication\Admin_Listing_Column;
use JetApplication\WarehouseManagement_StockCard;

class Listing_Column_InStock extends Admin_Listing_Column
{
	public const KEY = 'in_stock';
	
	public function getTitle(): string
	{
		return Tr::_('Units in stock');
	}
	
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): float
	{
		/**
		 * @var WarehouseManagement_StockCard $item
		 */
		return $item->getInStock();
	}
	
}
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
use JetApplication\WarehouseManagement_Warehouse;

class Listing_Column_Warehouse extends Admin_Listing_Column
{
	public const KEY = 'warehouse';
	
	public function getDisallowSort() : bool
	{
		return true;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Warehouse');
	}
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): string
	{
		/**
		 * @var WarehouseManagement_StockCard $item
		 */
		return WarehouseManagement_Warehouse::getScope()[$item->getWarehouseId()]??'';
	}
	
	
}
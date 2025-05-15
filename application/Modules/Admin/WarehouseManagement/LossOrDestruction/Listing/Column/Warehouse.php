<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\LossOrDestruction;

use Jet\Tr;
use JetApplication\Admin_Listing_Column;
use JetApplication\WarehouseManagement_LossOrDestruction;
use JetApplication\WarehouseManagement_Warehouse;

class Listing_Column_Warehouse extends Admin_Listing_Column
{
	public const KEY = 'warehouse_id';
	
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
		 * @var WarehouseManagement_LossOrDestruction $item
		 */
		return WarehouseManagement_Warehouse::getScope()[$item->getWarehouseId()]??'';
	}
	
	
}
<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\TransferBetweenWarehouses;

use Jet\Tr;
use Jet\UI_dataGrid_column;
use JetApplication\Admin_Listing_Column;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses;
use JetApplication\WarehouseManagement_Warehouse;

class Listing_Column_TargetWh extends Admin_Listing_Column
{
	public const KEY = 'target_wh';
	
	public function getTitle(): string
	{
		return Tr::_('Target warehouse');
	}
	
	public function initializer( UI_dataGrid_column $column ): void
	{
		//$column->addCustomCssStyle('width: 200px');
	}
	
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): object|string
	{
		/**
		 * @var WarehouseManagement_TransferBetweenWarehouses $item
		 */
		return WarehouseManagement_Warehouse::getScope()[$item->getTargetWarehouseId()]??'';
	}
	
	
}
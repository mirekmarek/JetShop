<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\TransferBetweenWarehouses;

use JetApplication\Admin_Listing_Filter_StdFilter;
use JetApplication\WarehouseManagement_Warehouse;

class Listing_Filter_TargetWh extends Admin_Listing_Filter_StdFilter
{
	public const KEY = 'target_wh';
	protected string $label = 'Target warehouse';
	
	protected function getOptions() : array
	{
		return WarehouseManagement_Warehouse::getScope();
	}
	
	public function generateWhere(): void
	{
		if(!$this->value) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'target_warehouse_id'   => $this->value,
		]);
	}
	
}
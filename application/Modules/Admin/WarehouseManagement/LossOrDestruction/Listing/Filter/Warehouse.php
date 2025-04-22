<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\LossOrDestruction;

use JetApplication\Admin_Listing_Filter_StdFilter;
use JetApplication\WarehouseManagement_Warehouse;


class Listing_Filter_Warehouse extends Admin_Listing_Filter_StdFilter
{
	public const KEY = 'warehouse';
	protected string $label = 'Warehouse';
	
	protected function getOptions(): array
	{
		return WarehouseManagement_Warehouse::getScope();
	}
	
	public function generateWhere(): void
	{
		if(!$this->value) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'warehouse_id'   => $this->value,
		]);
	}
	
}
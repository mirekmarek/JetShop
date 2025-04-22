<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\TransferBetweenWarehouses;

use JetApplication\Admin_Listing_Filter_StdFilter;
use JetApplication\Supplier;

class Listing_Filter_Supplier extends Admin_Listing_Filter_StdFilter
{
	public const KEY = 'supplier';
	protected string $label = 'Supplier';
	
	protected function getOptions(): array
	{
		return Supplier::getScope();
	}
	
	public function generateWhere(): void
	{
		if(!$this->value) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'supplier_id'   => $this->value,
		]);
	}
	
}
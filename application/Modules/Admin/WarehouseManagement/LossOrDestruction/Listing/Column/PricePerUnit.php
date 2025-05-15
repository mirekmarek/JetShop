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

class Listing_Column_PricePerUnit extends Admin_Listing_Column
{
	public const KEY = 'price_per_unit';
	
	public function getTitle(): string
	{
		return Tr::_('Price per unit');
	}
	
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): float
	{
		/**
		 * @var WarehouseManagement_LossOrDestruction $item
		 */
		return $item->getPricePerUnit();
	}
	
	
}
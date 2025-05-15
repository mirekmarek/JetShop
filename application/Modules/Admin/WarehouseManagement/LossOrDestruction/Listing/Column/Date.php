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

class Listing_Column_Date extends Admin_Listing_Column
{
	public const KEY = 'date';
	
	public function getTitle(): string
	{
		return Tr::_('Date');
	}
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): object
	{
		/**
		 * @var WarehouseManagement_LossOrDestruction $item
		 */
		return $item->getDate();
	}
}
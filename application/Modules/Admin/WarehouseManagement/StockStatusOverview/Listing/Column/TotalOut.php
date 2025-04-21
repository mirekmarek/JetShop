<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\StockStatusOverview;

use Jet\Tr;
use JetApplication\Admin_Listing_Column;

class Listing_Column_TotalOut extends Admin_Listing_Column
{
	public const KEY = 'total_out';
	
	public function getTitle(): string
	{
		return Tr::_('Total out');
	}
}
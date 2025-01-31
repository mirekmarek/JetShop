<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\WarehouseManagement\StockStatusOverview;


use Jet\DataListing_Column;
use Jet\Tr;

class Listing_Column_Warehouse extends DataListing_Column
{
	public const KEY = 'warehouse';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getDisallowSort() : bool
	{
		return true;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Warehouse');
	}
	
	
}
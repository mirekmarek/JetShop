<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\StockStatusOverview;


use Jet\DataListing_Column;
use Jet\Tr;

class Listing_Column_Product extends DataListing_Column
{
	public const KEY = 'product';
	
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
		return Tr::_('Product');
	}
	
	
}
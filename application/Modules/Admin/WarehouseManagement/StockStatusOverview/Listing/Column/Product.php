<?php

/**
 *
 * @copyright
 * @license
 * @author
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
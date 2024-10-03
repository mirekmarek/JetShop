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

class Listing_Column_Rack extends DataListing_Column
{
	public const KEY = 'rack';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Rack');
	}
	
	
}
<?php

/**
 *
 * @copyright
 * @license
 * @author
 */

namespace JetApplicationModule\Admin\OrderPersonalReceipt\Overview;

use Jet\DataListing_Column;
use Jet\Tr;

class Listing_Column_Warehouse extends DataListing_Column
{
	public const KEY = 'warehouse';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Warehouse');
	}
	
	
}
<?php

/**
 *
 * @copyright
 * @license
 * @author
 */

namespace JetApplicationModule\Admin\Orders;

use Jet\DataListing_Column;
use Jet\Tr;

class Listing_Column_DatePurchased extends DataListing_Column
{
	public const KEY = 'date_purchased';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Date purchased');
	}
}
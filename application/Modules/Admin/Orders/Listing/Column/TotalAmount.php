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

class Listing_Column_TotalAmount extends DataListing_Column
{
	public const KEY = 'total_amount';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Amount');
	}
}
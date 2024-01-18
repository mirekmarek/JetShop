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

class Listing_Column_Customer extends DataListing_Column
{
	public const KEY = 'customer';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Customer');
	}
	
	public function getDisallowSort(): bool
	{
		return true;
	}
}
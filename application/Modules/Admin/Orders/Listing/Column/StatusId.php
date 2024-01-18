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

class Listing_Column_StatusId extends DataListing_Column
{
	public const KEY = 'status_id';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Status');
	}
}
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

class Listing_Column_DispatchDate extends DataListing_Column
{
	public const KEY = 'dispatch_date';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Day of dispatch');
	}
}
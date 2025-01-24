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

class Listing_Column_Order extends DataListing_Column
{
	public const KEY = 'order_number';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Order');
	}
}
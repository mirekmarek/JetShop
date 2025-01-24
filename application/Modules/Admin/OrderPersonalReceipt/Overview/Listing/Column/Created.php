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

class Listing_Column_Created extends DataListing_Column
{
	public const KEY = 'created';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Date and time of creation');
	}
}
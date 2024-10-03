<?php

/**
 *
 * @copyright
 * @license
 * @author
 */

namespace JetApplicationModule\Admin\DeliveryNotes;

use Jet\DataListing_Column;
use Jet\Tr;

class Listing_Column_Total extends DataListing_Column
{
	public const KEY = 'total';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Total');
	}
}
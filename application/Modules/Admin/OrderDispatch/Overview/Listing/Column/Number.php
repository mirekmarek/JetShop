<?php

/**
 *
 * @copyright
 * @license
 * @author
 */

namespace JetApplicationModule\Admin\OrderDispatch\Overview;

use Jet\DataListing_Column;
use Jet\Tr;

class Listing_Column_Number extends DataListing_Column
{
	public const KEY = 'number';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Number');
	}
}
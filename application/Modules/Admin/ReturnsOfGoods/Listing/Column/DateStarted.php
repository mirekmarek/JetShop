<?php

/**
 *
 * @copyright
 * @license
 * @author
 */

namespace JetApplicationModule\Admin\ReturnsOfGoods;

use Jet\DataListing_Column;
use Jet\Tr;

class Listing_Column_DateStarted extends DataListing_Column
{
	public const KEY = 'date_started';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Date and time');
	}
}
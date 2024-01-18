<?php

/**
 *
 * @copyright
 * @license
 * @author
 */

namespace JetApplicationModule\Admin\Customers;

use Jet\DataListing_Column;
use Jet\Tr;

class Listing_Column_Name extends DataListing_Column
{
	public const KEY = 'name';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Name');
	}
}
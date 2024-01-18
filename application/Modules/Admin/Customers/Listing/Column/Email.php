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

class Listing_Column_Email extends DataListing_Column
{
	public const KEY = 'email';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('e-mail');
	}
}
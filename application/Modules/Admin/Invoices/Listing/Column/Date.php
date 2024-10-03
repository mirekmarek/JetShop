<?php

/**
 *
 * @copyright
 * @license
 * @author
 */

namespace JetApplicationModule\Admin\Invoices;

use Jet\DataListing_Column;
use Jet\Tr;

class Listing_Column_Date extends DataListing_Column
{
	public const KEY = 'invoice_date';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Date');
	}
}
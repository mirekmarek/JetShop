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

class Listing_Column_Recipient extends DataListing_Column
{
	public const KEY = 'recipient';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Recipient');
	}
	
	public function getDisallowSort(): bool
	{
		return true;
	}
}
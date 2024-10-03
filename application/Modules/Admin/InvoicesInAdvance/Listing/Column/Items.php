<?php

/**
 *
 * @copyright
 * @license
 * @author
 */

namespace JetApplicationModule\Admin\InvoicesInAdvance;

use Jet\DataListing_Column;
use Jet\Tr;

class Listing_Column_Items extends DataListing_Column
{
	public const KEY = 'items';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Items');
	}
	
	public function getDisallowSort(): bool
	{
		return true;
	}
}
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

class Listing_Column_Context extends DataListing_Column
{
	public const KEY = 'context';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Context');
	}
	
	public function getDisallowSort(): bool
	{
		return true;
	}
}
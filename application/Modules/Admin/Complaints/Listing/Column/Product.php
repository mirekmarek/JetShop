<?php

/**
 *
 * @copyright
 * @license
 * @author
 */

namespace JetApplicationModule\Admin\Complaints;

use Jet\DataListing_Column;
use Jet\Tr;

class Listing_Column_Product extends DataListing_Column
{
	public const KEY = 'product_id';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Product');
	}
}
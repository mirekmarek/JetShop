<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Marketing\Banners;

use Jet\DataListing_Column;
use Jet\Tr;

class Listing_Column_MinimalOrderAmount extends DataListing_Column
{
	public const KEY = 'minimal_order_amount';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_( 'Minimal order amount' );
	}
}


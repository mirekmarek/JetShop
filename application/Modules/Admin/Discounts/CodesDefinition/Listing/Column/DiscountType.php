<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Discounts\CodesDefinition;

use Jet\DataListing_Column;
use Jet\Tr;

class Listing_Column_DiscountType extends DataListing_Column
{
	public const KEY = 'discount_type';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_( 'Discount type' );
	}
}
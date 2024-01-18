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

class Listing_Column_InternalNotes extends DataListing_Column
{
	public const KEY = 'internal_notes';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_( 'Internal notes' );
	}
}
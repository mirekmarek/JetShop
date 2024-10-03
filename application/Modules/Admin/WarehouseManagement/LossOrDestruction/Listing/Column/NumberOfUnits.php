<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\WarehouseManagement\LossOrDestruction;

use Jet\DataListing_Column;
use Jet\Tr;

class Listing_Column_NumberOfUnits extends DataListing_Column
{
	public const KEY = 'number_of_units';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Number of units');
	}
	
}
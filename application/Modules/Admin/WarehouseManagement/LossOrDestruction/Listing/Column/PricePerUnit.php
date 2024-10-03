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

class Listing_Column_PricePerUnit extends DataListing_Column
{
	public const KEY = 'price_per_unit';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Price per unit');
	}
	
}
<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\SupplierGoodsOrders;

use Jet\DataListing_Column;
use Jet\Tr;
use Jet\UI_dataGrid_column;

class Listing_Column_Notes extends DataListing_Column
{
	public const KEY = 'notes';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Notes');
	}
	
	public function getDisallowSort() : bool
	{
		return true;
	}
	
	
	public function initializer( UI_dataGrid_column $column ): void
	{
		//$column->addCustomCssStyle('width: 200px');
	}
	
}
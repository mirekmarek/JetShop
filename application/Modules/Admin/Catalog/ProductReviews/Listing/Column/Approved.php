<?php
/**
 *
 * @copyright
 * @license
 * @author
 */

namespace JetApplicationModule\Admin\Catalog\ProductReviews;

use Jet\DataListing_Column;
use Jet\Tr;
use Jet\UI_dataGrid_column;

class Listing_Column_Approved extends DataListing_Column
{
	public const KEY = 'approved';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Approved');
	}
	
	public function initializer( UI_dataGrid_column $column ): void
	{
		$column->addCustomCssStyle('width:80px');
	}
}
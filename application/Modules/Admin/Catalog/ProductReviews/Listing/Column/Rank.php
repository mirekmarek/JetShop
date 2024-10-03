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

class Listing_Column_Rank extends DataListing_Column
{
	public const KEY = 'rank';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Rank');
	}
	
	public function initializer( UI_dataGrid_column $column ): void
	{
		$column->addCustomCssStyle('width:80px');
	}
}
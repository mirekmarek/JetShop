<?php
/**
 *
 * @copyright
 * @license
 * @author
 */

namespace JetApplicationModule\Admin\Catalog\ProductQuestions;

use Jet\DataListing_Column;
use Jet\Tr;
use Jet\UI_dataGrid_column;

class Listing_Column_Answered extends DataListing_Column
{
	public const KEY = 'answered';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Answered');
	}
	
	public function initializer( UI_dataGrid_column $column ): void
	{
		$column->addCustomCssStyle('width:80px');
	}
}
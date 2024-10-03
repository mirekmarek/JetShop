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
use Jet\UI_dataGrid_column;

class Listing_Column_Code extends DataListing_Column
{
	public const KEY = 'code';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_( 'Code' );
	}
	
	public function initializer( UI_dataGrid_column $column ): void
	{
		$column->setBaseCssClass("width: 250px;");
	}
}
<?php
namespace JetApplicationModule\Admin\Catalog\Products;


use Jet\DataListing_Column;
use Jet\UI_dataGrid_column;

class Listing_Column_Edit extends DataListing_Column
{
	public const KEY = 'edit';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function isMandatory(): bool
	{
		return true;
	}
	
	public function getDisallowSort(): bool
	{
		return true;
	}
	
	public function getTitle(): string
	{
		return '';
	}
	
	public function initializer( UI_dataGrid_column $column ): void
	{
		$column->addCustomCssStyle('width:20px');
	}
}
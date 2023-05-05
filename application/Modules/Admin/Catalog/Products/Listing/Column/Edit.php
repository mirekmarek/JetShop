<?php
namespace JetApplicationModule\Admin\Catalog\Products;


use Jet\UI_dataGrid_column;

class Listing_Column_Edit extends Listing_Column
{
	
	public static function getKey(): string
	{
		return 'edit';
	}
	
	public function isMandatory(): bool
	{
		return true;
	}
	
	public function getDisallowSort(): bool
	{
		return true;
	}
	
	public static function getTitle(): string
	{
		return '';
	}
	
	public function initializer( UI_dataGrid_column $column ): void
	{
		$column->addCustomCssStyle('width:20px');
	}
}
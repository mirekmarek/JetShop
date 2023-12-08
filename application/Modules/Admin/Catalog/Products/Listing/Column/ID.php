<?php
namespace JetApplicationModule\Admin\Catalog\Products;


use Jet\DataListing_Column;
use Jet\Tr;
use Jet\UI_dataGrid_column;

class Listing_Column_ID extends DataListing_Column
{
	public const KEY = 'id';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('ID');
	}
	
	
	public function initializer( UI_dataGrid_column $column ): void
	{
		$column->addCustomCssStyle('width:120px');
	}
	
	public function getExportHeader(): null|string|array
	{
		return 'ID';
	}
	
	public function getExportData( mixed $item ): float|int|bool|string|array
	{
		/**
		 * @var Product $item
		 */
		return $item->getId();
	}
}
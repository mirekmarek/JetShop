<?php
namespace JetShopModule\Admin\Catalog\Products;


use Jet\UI_dataGrid_column;
use JetShop\Product;

class Listing_Column_ID extends Listing_Column
{
	
	public function getKey(): string
	{
		return 'id';
	}
	
	public function getTitle(): string
	{
		return 'ID';
	}
	
	
	public function initializer( UI_dataGrid_column $column ): void
	{
		$column->addCustomCssStyle('width:120px');
	}
	
	public function getExportHeader(): null|string|array
	{
		return 'ID';
	}
	
	public function getExportData( Product $item ): float|int|bool|string|array
	{
		return $item->getId();
	}
}
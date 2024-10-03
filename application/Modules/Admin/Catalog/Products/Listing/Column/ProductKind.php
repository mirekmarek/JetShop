<?php
namespace JetApplicationModule\Admin\Catalog\Products;


use Jet\DataListing_Column;
use Jet\Tr;

class Listing_Column_ProductKind extends DataListing_Column
{
	public const KEY = 'kind_of_product';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getOrderByDesc(): array|string
	{
		return '-kind_id';
	}
	
	public function getOrderByAsc(): array|string
	{
		return '+kind_id';
	}
	
	public function getTitle(): string
	{
		return Tr::_('Kind of product');
	}
	
	public function getExportHeader(): null|string|array
	{
		return Tr::_('Kind of product');
	}
	
	public function getExportData( mixed $item ): float|int|bool|string|array
	{
		/**
		 * @var Product $item
		 */
		return $item->getKindOfProduct()?->getInternalName()?:'';
	}
}
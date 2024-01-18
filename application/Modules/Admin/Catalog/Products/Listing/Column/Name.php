<?php
namespace JetApplicationModule\Admin\Catalog\Products;


use Jet\DataListing_Column;
use Jet\Tr;
use JetApplication\Shops;

class Listing_Column_Name extends DataListing_Column
{
	public const KEY = 'name';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getOrderByAsc(): array|string
	{
		return '+products_shop_data.name';
	}
	
	public function getOrderByDesc(): array|string
	{
		return '-products_shop_data.name';
	}

	public function getTitle(): string
	{
		return Tr::_('Name');
	}
	
	public function getExportHeader(): null|string|array
	{
		$headers = [];
		
		foreach(Shops::getListSorted() as $shop ) {
			$headers[] = 'Name - '.$shop->getShopName();
		}
		
		return $headers;
	}
	
	public function getExportData( mixed $item ): float|int|bool|string|array
	{
		/**
		 * @var Product $item
		 */
		$data = [];
		
		foreach(Shops::getListSorted() as $shop ) {
			$data[] = $item->getShopData($shop)->getFullName();
		}
		
		return $data;
	}
}
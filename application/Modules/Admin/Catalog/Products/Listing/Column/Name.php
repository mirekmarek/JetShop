<?php
namespace JetApplicationModule\Admin\Catalog\Products;


use JetApplication\Product;
use JetApplication\Shops;

class Listing_Column_Name extends Listing_Column
{
	
	public static function getKey(): string
	{
		return 'name';
	}
	
	public function getOrderByAsc(): array|string
	{
		return '+products_shop_data.name';
	}
	
	public function getOrderByDesc(): array|string
	{
		return '-products_shop_data.name';
	}

	public static function getTitle(): string
	{
		return 'Name';
	}
	
	public function getExportHeader(): null|string|array
	{
		$headers = [];
		
		foreach(Shops::getListSorted() as $shop ) {
			$headers[] = 'Name - '.$shop->getShopName();
		}
		
		return $headers;
	}
	
	public function getExportData( Product $item ): float|int|bool|string|array
	{
		$data = [];
		
		foreach(Shops::getListSorted() as $shop ) {
			$data[] = $item->getShopData($shop)->getName();
		}
		
		return $data;
	}
}
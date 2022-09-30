<?php
namespace JetShopModule\Admin\Catalog\Products;


use JetShop\Product;
use JetShop\Shops;

class Listing_Column_Name extends Listing_Column
{
	
	public function getKey(): string
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

	public function getTitle(): string
	{
		return 'Name';
	}
	
	public function getExportHeader(): null|string|array
	{
		$headers = [];
		
		foreach(Shops::getList() as $shop ) {
			$headers[] = 'Name - '.$shop->getShopName();
		}
		
		return $headers;
	}
	
	public function getExportData( Product $item ): float|int|bool|string|array
	{
		$data = [];
		
		foreach(Shops::getList() as $shop ) {
			$data[] = $item->getShopData($shop)->getName();
		}
		
		return $data;
	}
}
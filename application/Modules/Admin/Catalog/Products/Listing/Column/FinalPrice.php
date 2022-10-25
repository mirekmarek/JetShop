<?php
namespace JetShopModule\Admin\Catalog\Products;


use JetShop\Product;
use JetShop\Shops;

class Listing_Column_FinalPrice extends Listing_Column
{
	
	public static function getKey(): string
	{
		return 'final_price';
	}
	
	public function getOrderByAsc(): array|string
	{
		return '+products_shop_data.final_price';
	}
	
	public function getOrderByDesc(): array|string
	{
		return '-products_shop_data.final_price';
	}

	public static function getTitle(): string
	{
		return 'Price';
	}
	
	public function getExportHeader(): null|string|array
	{
		$headers = [];
		
		foreach(Shops::getListSorted() as $shop ) {
			$headers[] = 'Price - '.$shop->getShopName();
		}
		
		return $headers;
	}
	
	public function getExportData( Product $item ): float|int|bool|string|array
	{
		$data = [];
		
		foreach(Shops::getListSorted() as $shop ) {
			$data[] = $item->getShopData($shop)->getFinalPrice();
		}
		
		return $data;
	}
}
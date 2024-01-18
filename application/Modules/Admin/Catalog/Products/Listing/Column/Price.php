<?php
namespace JetApplicationModule\Admin\Catalog\Products;


use Jet\DataListing_Column;
use Jet\Tr;
use JetApplication\Shops;

class Listing_Column_Price extends DataListing_Column
{
	public const KEY = 'price';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getOrderByAsc(): array|string
	{
		return '+products_shop_data.price';
	}
	
	public function getOrderByDesc(): array|string
	{
		return '-products_shop_data.price';
	}

	public function getTitle(): string
	{
		return Tr::_('Price');
	}
	
	public function getExportHeader(): null|string|array
	{
		$headers = [];
		
		foreach(Shops::getListSorted() as $shop ) {
			$headers[$shop->getKey()] = 'Price - '.$shop->getShopName();
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
			$data[$shop->getKey()] = $item->getShopData($shop)->getPrice();
		}
		
		return $data;
	}
}
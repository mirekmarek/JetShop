<?php
namespace JetApplicationModule\Admin\Catalog\Products;


use Jet\DataListing_Column;
use Jet\Tr;
use JetApplication\Shops;

class Listing_Column_FinalPrice extends DataListing_Column
{
	public const KEY = 'final_price';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getOrderByAsc(): array|string
	{
		return '+products_shop_data.final_price';
	}
	
	public function getOrderByDesc(): array|string
	{
		return '-products_shop_data.final_price';
	}

	public function getTitle(): string
	{
		return Tr::_('Price');
	}
	
	public function getExportHeader(): null|string|array
	{
		$headers = [];
		
		foreach(Shops::getListSorted() as $shop ) {
			$headers[] = 'Price - '.$shop->getShopName();
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
			$data[] = $item->getShopData($shop)->getPrice();
		}
		
		return $data;
	}
}
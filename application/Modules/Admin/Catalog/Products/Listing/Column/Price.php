<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Products;


use Jet\Tr;
use JetApplication\Admin_Listing_Column;
use JetApplication\Product;
use JetApplication\Pricelists;

class Listing_Column_Price extends Admin_Listing_Column
{
	public const KEY = 'price';
	
	public function getOrderByAsc(): array|string
	{
		return '+products_price.price';
	}
	
	public function getOrderByDesc(): array|string
	{
		return '-products_price.price';
	}

	public function getTitle(): string
	{
		return Tr::_('Price');
	}
	
	public function getExportHeader(): array
	{
		$headers = [];
		
		foreach(Pricelists::getList() as $pricelist) {
			$headers[$pricelist->getCode()] = 'Price - '.$pricelist->getName();
		}
		
		return $headers;
	}
	
	public function getExportData( mixed $item ): array
	{
		/**
		 * @var Product $item
		 */
		$data = [];
		
		foreach(Pricelists::getList() as $pricelist) {
			$data[$pricelist->getCode()] = $item->getPrice( $pricelist );
		}
		
		return $data;
	}
}
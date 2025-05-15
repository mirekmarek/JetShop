<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Products;



use Jet\Tr;
use JetApplication\Admin_Listing_Column;
use JetApplication\EShops;
use JetApplication\Product;

class Listing_Column_Name extends Admin_Listing_Column
{
	public const KEY = 'name';
	
	public function getOrderByAsc(): array|string
	{
		return '+products_eshop_data.name';
	}
	
	public function getOrderByDesc(): array|string
	{
		return '-products_eshop_data.name';
	}

	public function getTitle(): string
	{
		return Tr::_('Name');
	}
	
	public function getExportHeader(): array
	{
		$headers = [];
		
		foreach( EShops::getListSorted() as $eshop ) {
			$headers[] = 'Name - '.$eshop->getName();
		}
		
		return $headers;
	}
	
	public function getExportData( mixed $item ): array
	{
		/**
		 * @var Product $item
		 */
		$data = [];
		
		foreach( EShops::getListSorted() as $eshop ) {
			$data[] = $item->getEshopData($eshop)->getFullName();
		}
		
		return $data;
	}
}
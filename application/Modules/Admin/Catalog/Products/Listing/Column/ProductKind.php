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

class Listing_Column_ProductKind extends Admin_Listing_Column
{
	public const KEY = 'kind_of_product';

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
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): string
	{
		/**
		 * @var Product $item
		 */
		return $item->getKindOfProduct()?->getAdminTitle()?:'';
	}
}
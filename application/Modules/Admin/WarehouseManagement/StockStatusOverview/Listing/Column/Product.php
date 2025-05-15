<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\StockStatusOverview;

use Jet\Tr;
use JetApplication\Admin_Listing_Column;
use JetApplication\Product;
use JetApplication\WarehouseManagement_StockCard;

class Listing_Column_Product extends Admin_Listing_Column
{
	public const KEY = 'product';
	
	public function getDisallowSort() : bool
	{
		return true;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Product');
	}
	
	
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): string
	{
		/**
		 * @var WarehouseManagement_StockCard $item
		 */
		return Product::get($item->getProductId())?->getAdminTitle()??'';
	}
	
}
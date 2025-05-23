<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\SupplierGoodsOrders;

use Jet\Tr;
use Jet\UI_dataGrid_column;
use JetApplication\Admin_Listing_Column;
use JetApplication\Supplier_GoodsOrder;

class Listing_Column_Supplier extends Admin_Listing_Column
{
	public const KEY = 'supplier';
	
	public function getTitle(): string
	{
		return Tr::_('Supplier');
	}
	
	public function getDisallowSort() : bool
	{
		return true;
	}
	
	
	public function initializer( UI_dataGrid_column $column ): void
	{
		//$column->addCustomCssStyle('width: 200px');
	}
	
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): string
	{
		/**
		 * @var Supplier_GoodsOrder $item
		 */
		return $item->getSupplierCompanyName();
	}
	
	
}
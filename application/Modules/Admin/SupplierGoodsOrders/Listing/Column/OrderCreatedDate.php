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

class Listing_Column_OrderCreatedDate extends Admin_Listing_Column
{
	public const KEY = 'order_created_date';
	
	public function getTitle(): string
	{
		return Tr::_('Order Created Date');
	}
	
	public function initializer( UI_dataGrid_column $column ): void
	{
		//$column->addCustomCssStyle('width: 200px');
	}
	
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): object|string
	{
		/**
		 * @var Supplier_GoodsOrder $item
		 */
		return $item->getOrderCreatedDate()??'';
	}
	
}
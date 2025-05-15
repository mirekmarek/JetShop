<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\ReceiptOfGoods;

use Jet\Tr;
use Jet\UI_dataGrid_column;
use JetApplication\Admin_Listing_Column;
use JetApplication\WarehouseManagement_ReceiptOfGoods;

class Listing_Column_Items extends Admin_Listing_Column
{
	public const KEY = 'items';
	
	public function getTitle(): string
	{
		return Tr::_('Items');
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
		 * @var WarehouseManagement_ReceiptOfGoods $item
		 */
		$res = '';
		
		foreach($item->getItems() as $i) {
			$res .= $i->getUnitsReceived();
			$res .= $i->getMeasureUnit()?->getName()??'x';
			$res .= '  ';
			$res .= $i->getProductName();
			$res .= ",\n";
		}
		
		return $res;
	}
	
}
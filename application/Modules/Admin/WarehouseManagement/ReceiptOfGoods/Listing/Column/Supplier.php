<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\ReceiptOfGoods;

use Jet\Tr;
use JetApplication\Admin_Listing_Column;
use JetApplication\Supplier;
use JetApplication\WarehouseManagement_ReceiptOfGoods;

class Listing_Column_Supplier extends Admin_Listing_Column
{
	public const KEY = 'supplier';
	
	public function getTitle(): string
	{
		return Tr::_('Supplier');
	}
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getDisallowSort(): bool
	{
		return true;
	}
	
	public function getExportData( mixed $item ): string
	{
		/**
		 * @var WarehouseManagement_ReceiptOfGoods $item
		 */
		$supplier = Supplier::get( $item->getSupplierId() );
		if(!$supplier) {
			return '';
		}
		
		//TODO:
		return '';
	}
}
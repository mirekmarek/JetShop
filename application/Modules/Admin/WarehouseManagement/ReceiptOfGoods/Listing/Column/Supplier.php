<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\ReceiptOfGoods;


use Jet\DataListing_Column;
use Jet\Tr;
use JetApplication\Supplier;
use JetApplication\WarehouseManagement_ReceiptOfGoods;

class Listing_Column_Supplier extends DataListing_Column
{
	public const KEY = 'supplier';
	
	public function __construct()
	{
	}
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Supplier');
	}
	
	public function getExportHeader(): null|string|array
	{
		return 'Supplier';
	}
	
	public function getDisallowSort(): bool
	{
		return true;
	}
	
	public function getExportData( mixed $item ): float|int|bool|string|array
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
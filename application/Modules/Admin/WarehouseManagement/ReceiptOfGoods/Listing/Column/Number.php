<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\WarehouseManagement\ReceiptOfGoods;

use Jet\DataListing_Column;
use Jet\Tr;
use JetApplication\WarehouseManagement_ReceiptOfGoods;

class Listing_Column_Number extends DataListing_Column
{
	public const KEY = 'number';
	
	public function __construct()
	{
	}
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Number');
	}
	
	public function getExportHeader(): null|string|array
	{
		return 'Number';
	}
	
	public function getExportData( mixed $item ): float|int|bool|string|array
	{
		/**
		 * @var WarehouseManagement_ReceiptOfGoods $item
		 */
		return $item->getNumber();
	}
}
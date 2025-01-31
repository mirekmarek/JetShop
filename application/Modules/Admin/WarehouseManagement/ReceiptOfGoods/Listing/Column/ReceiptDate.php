<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\ReceiptOfGoods;


use Jet\DataListing_Column;
use Jet\Locale;
use Jet\Tr;
use JetApplication\WarehouseManagement_ReceiptOfGoods;

class Listing_Column_ReceiptDate extends DataListing_Column
{
	public const KEY = 'receipt_date';
	
	public function __construct()
	{
	}
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Receipt Date');
	}
	
	public function getExportHeader(): null|string|array
	{
		return 'Posting Date';
	}
	
	public function getExportData( mixed $item ): float|int|bool|string|array
	{
		/**
		 * @var WarehouseManagement_ReceiptOfGoods $item
		 */
		return Locale::date( $item->getReceiptDate() );
	}
}
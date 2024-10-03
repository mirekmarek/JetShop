<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\WarehouseManagement\StockVerification;

use Jet\DataListing_Column;
use Jet\Locale;
use Jet\Tr;

class Listing_Column_Date extends DataListing_Column
{
	public const KEY = 'date';
	
	public function __construct()
	{
	}
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Date');
	}
	
	public function getExportHeader(): null|string|array
	{
		return 'Date';
	}
	
	public function getExportData( mixed $item ): float|int|bool|string|array
	{
		/**
		 * @var Verification $item
		 */
		return Locale::date( $item->getDate() );
	}
}
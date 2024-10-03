<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\WarehouseManagement\TransferBetweenWarehouses;

use Jet\DataListing_Column;
use Jet\Tr;

class Listing_Column_Notes extends DataListing_Column
{
	public const KEY = 'notes';
	
	public function __construct()
	{
	}
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Notes');
	}
	
	public function getExportHeader(): null|string|array
	{
		return 'Notes';
	}
	
	public function getDisallowSort() : bool
	{
		return true;
	}
	
	public function getExportData( mixed $item ): float|int|bool|string|array
	{
		/**
		 * @var Transfer $item
		 */
		return $item->getNumber();
	}
}
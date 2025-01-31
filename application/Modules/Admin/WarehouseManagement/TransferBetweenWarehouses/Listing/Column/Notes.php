<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\TransferBetweenWarehouses;


use Jet\DataListing_Column;
use Jet\Tr;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses;

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
		 * @var WarehouseManagement_TransferBetweenWarehouses $item
		 */
		return $item->getNumber();
	}
}
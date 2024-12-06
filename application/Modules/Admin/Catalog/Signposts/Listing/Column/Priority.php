<?php
namespace JetApplicationModule\Admin\Catalog\Signposts;


use Jet\DataListing_Column;
use Jet\Tr;
use Jet\UI_dataGrid_column;
use JetApplication\Signpost;

class Listing_Column_Priority extends DataListing_Column
{
	public const KEY = 'priority';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Priority');
	}
	
	public function getExportHeader(): null|string|array
	{
		return 'priority';
	}
	
	public function getExportData( mixed $item ): float|int|bool|string|array
	{
		/**
		 * @var Signpost $item
		 */
		return $item->getPriority();
	}
	
	public function initializer( UI_dataGrid_column $column ): void
	{
		$column->addCustomCssStyle('width: 200px;');
	}
}
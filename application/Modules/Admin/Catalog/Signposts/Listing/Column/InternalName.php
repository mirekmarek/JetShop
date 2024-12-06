<?php
namespace JetApplicationModule\Admin\Catalog\Signposts;


use Jet\DataListing_Column;
use Jet\Tr;
use JetApplication\Signpost;

class Listing_Column_InternalName extends DataListing_Column
{
	public const KEY = 'internal_name';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Internal name');
	}
	
	public function getExportHeader(): null|string|array
	{
		return 'internal_name';
	}
	
	public function getExportData( mixed $item ): float|int|bool|string|array
	{
		/**
		 * @var Signpost $item
		 */
		return $item->getInternalName();
	}
}
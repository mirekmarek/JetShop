<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Entity\Listing;


use Jet\Tr;
use JetApplication\Admin_Listing_Column;
use JetApplication\EShopEntity_WithEShopData;

class Listing_Column_InternalNotes extends Admin_Listing_Column
{
	public const KEY = 'internal_notes';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Internal notes', dictionary: Tr::COMMON_DICTIONARY);
	}
	
	public function getDisallowSort(): bool
	{
		return true;
	}
	
	
	public function getExportHeader(): string
	{
		return Tr::_('Internal notes', dictionary: Tr::COMMON_DICTIONARY);
	}
	
	public function getExportData( mixed $item ): string
	{
		/**
		 * @var EShopEntity_WithEShopData $item
		 */
		return $item->getInternalNotes();
	}
	
	
}
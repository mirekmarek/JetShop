<?php
/**
 *
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license http://www.php-jet.net/license/license.txt
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Entity\Listing;

use Jet\Tr;
use JetApplication\Entity_WithShopData;

class Listing_Column_InternalNotes extends Listing_Column_Abstract
{
	public const KEY = 'internal_notes';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Internal notes');
	}
	
	public function getDisallowSort(): bool
	{
		return true;
	}
	
	
	public function getExportHeader(): string
	{
		return Tr::_('Internal notes');
	}
	
	public function getExportData( mixed $item ): string
	{
		/**
		 * @var Entity_WithShopData $item
		 */
		return $item->getInternalNotes();
	}
	
	
}
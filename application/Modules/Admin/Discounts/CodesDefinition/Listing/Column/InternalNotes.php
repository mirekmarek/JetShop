<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Discounts\CodesDefinition;

use Jet\Tr;
use JetApplication\Admin_Listing_Column;
use JetApplication\Discounts_Code;

class Listing_Column_InternalNotes extends Admin_Listing_Column
{
	public const KEY = 'internal_notes';
	
	public function getTitle(): string
	{
		return Tr::_( 'Internal notes' );
	}
	
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): string
	{
		/**
		 * @var Discounts_Code $item
		 */
		return $item->getInternalNotes();
	}
	
}
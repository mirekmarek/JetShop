<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Signposts;

use Jet\Tr;
use JetApplication\Admin_Listing_Column;
use JetApplication\Signpost;

class Listing_Column_InternalName extends Admin_Listing_Column
{
	public const KEY = 'internal_name';
	
	public function getTitle(): string
	{
		return Tr::_('Internal name');
	}
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): string
	{
		/**
		 * @var Signpost $item
		 */
		return $item->getInternalName();
	}
}
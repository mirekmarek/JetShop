<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\OrderDispatch\Overview;

use Jet\Tr;
use JetApplication\Admin_Listing_Column;
use JetApplication\OrderDispatch;

class Listing_Column_Created extends Admin_Listing_Column
{
	public const KEY = 'created';
	
	public function getTitle(): string
	{
		return Tr::_('Date and time of creation');
	}
	
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): object|string
	{
		/**
		 * @var OrderDispatch $item
		 */
		
		return $item->getCreated()??'';
	}
	
}
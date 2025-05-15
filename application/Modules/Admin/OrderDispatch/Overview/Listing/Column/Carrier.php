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

class Listing_Column_Carrier extends Admin_Listing_Column
{
	public const KEY = 'carrier';
	
	public function getTitle(): string
	{
		return Tr::_('Carrier');
	}
	
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): string
	{
		/**
		 * @var OrderDispatch $item
		 */
		
		return $item->getCarrierService()?->getName()??'';
	}
}
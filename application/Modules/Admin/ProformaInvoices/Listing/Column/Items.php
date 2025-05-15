<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ProformaInvoices;

use Jet\Tr;
use JetApplication\Admin_Listing_Column;
use JetApplication\ProformaInvoice;

class Listing_Column_Items extends Admin_Listing_Column
{
	public const KEY = 'items';
	
	public function getTitle(): string
	{
		return Tr::_('Items');
	}
	
	public function getDisallowSort(): bool
	{
		return true;
	}
	
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): string
	{
		/**
		 * @var ProformaInvoice $item
		 */
		$res = '';
		
		foreach($item->getItems() as $item) {
			$res .= $item->getNumberOfUnits();
			$res .= $item->getMeasureUnit()?->getName()??'x';
			$res .= '  ';
			$res .= $item->getTitle();
			$res .= ",\n";
		}
		
		return $res;
	}
	
}
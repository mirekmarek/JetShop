<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Customers;

use Jet\Tr;
use JetApplication\Admin_Listing_Column;
use JetApplication\Customer;

class Listing_Column_Name extends Admin_Listing_Column
{
	public const KEY = 'name';
	
	public function getTitle(): string
	{
		return Tr::_('Name');
	}
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): string
	{
		/**
		 * @var Customer $item
		 */
		
		return $item->getName();
	}
	
}
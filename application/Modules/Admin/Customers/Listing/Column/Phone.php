<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Customers;


use Jet\DataListing_Column;
use Jet\Tr;
use JetApplication\Customer;

class Listing_Column_Phone extends DataListing_Column
{
	public const KEY = 'phone_number';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Phone');
	}
	
	
	public function getExportHeader(): null|string|array
	{
		return Tr::_('Phone');
	}
	
	public function getExportData( mixed $item ): string
	{
		/**
		 * @var Customer $item
		 */
		
		return $item->getPhoneNumber();
	}
	
}
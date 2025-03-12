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

class Listing_Column_Email extends DataListing_Column
{
	public const KEY = 'email';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('e-mail');
	}
	
	public function getExportHeader(): null|string|array
	{
		return Tr::_('e-mail');
	}
	
	public function getExportData( mixed $item ): string
	{
		/**
		 * @var Customer $item
		 */
		
		return $item->getEmail();
	}
	
}
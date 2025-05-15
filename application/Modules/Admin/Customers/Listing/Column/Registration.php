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

class Listing_Column_Registration extends Admin_Listing_Column
{
	public const KEY = 'registration_date_time';
	
	public function getTitle(): string
	{
		return Tr::_('Registration');
	}
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): object|string
	{
		/**
		 * @var Customer $item
		 */
		return $item->getRegistrationDateTime()??'';
	}
}
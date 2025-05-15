<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\OrderDispatch\Overview;

use Jet\Tr;
use JetApplication\Admin_Listing_Column;
use JetApplication\CustomerBlacklist;
use JetApplication\OrderDispatch;

class Listing_Column_Recipient extends Admin_Listing_Column
{
	public const KEY = 'recipient';
	
	public function getTitle(): string
	{
		return Tr::_('Recipient');
	}
	
	public function getDisallowSort(): bool
	{
		return true;
	}
	
	
	public function getExportHeader(): array
	{
		return [
 			'company' => Tr::_('Company name'),
			'first_name' => Tr::_('First name'),
			'surname' => Tr::_('Surname'),
		    'address' => Tr::_('Address'),
		    'town' => Tr::_('Town'),
		    'zip' => Tr::_('ZIP'),
		    'country' => Tr::_('Country'),
			'email' => Tr::_('e-mail'),
			'phone' => Tr::_('Phone'),
			'blacklisted' => Tr::_('Customer is blacklisted'),
		];
	}
	
	public function getExportData( mixed $item ): array
	{
		/**
		 * @var OrderDispatch $item
		 */
		
		return [
			'company' => $item->getRecipientCompany(),
			'first_name' => $item->getRecipientFirstName(),
			'surname' => $item->getRecipientSurname(),
			'address' => $item->getRecipientStreet(),
			'town' => $item->getRecipientTown(),
			'zip' => $item->getRecipientZip(),
			'country' => $item->getRecipientCountry(),
			'email' => $item->getRecipientEmail(),
			'phone' => $item->getRecipientPhone(),
			'blacklisted' => CustomerBlacklist::customerIsBlacklisted( $item->getRecipientEmail() ),
		];
	}
	
	
}
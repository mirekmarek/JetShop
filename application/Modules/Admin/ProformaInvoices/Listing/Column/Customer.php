<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ProformaInvoices;

use Jet\Tr;
use Jet\UI_dataGrid_column;
use JetApplication\Admin_Listing_Column;
use JetApplication\CustomerBlacklist;
use JetApplication\ProformaInvoice;

class Listing_Column_Customer extends Admin_Listing_Column
{
	public const KEY = 'customer';
	
	public function getTitle(): string
	{
		return Tr::_('Customer');
	}
	
	public function getDisallowSort(): bool
	{
		return true;
	}
	
	public function initializer( UI_dataGrid_column $column ): void
	{
		$column->addCustomCssStyle('width:300px;');
	}
	
	
	public function getExportHeader(): array
	{
		return [
			'id' => Tr::_('Customer ID'),
			'company' => Tr::_('Company name'),
			'first_name' => Tr::_('First name'),
			'surname' => Tr::_('Surname'),
			'email' => Tr::_('e-mail'),
			'phone' => Tr::_('Phone'),
			'blacklisted' => Tr::_('Customer is blacklisted'),
		];
	}
	
	public function getExportData( mixed $item ): array
	{
		/**
		 * @var ProformaInvoice $item
		 */
		
		return [
			'id' => $item->getCustomerId(),
			'company' => $item->getCustomerCompanyName(),
			'first_name' => $item->getCustomerFirstName(),
			'surname' => $item->getCustomerSurname(),
			'email' => $item->getCustomerEmail(),
			'phone' => $item->getCustomerPhone(),
			'blacklisted' => CustomerBlacklist::customerIsBlacklisted( $item->getCustomerEmail() ),
		];
	}
	
}
<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ManageAccess\Administrators\Users;


use Jet\DataListing_Column;
use Jet\Tr;
use JetApplication\Auth_Administrator_User;

class Listing_Column_Surname extends DataListing_Column
{
	public const KEY = 'surname';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Surname');
	}
	
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): string
	{
		/**
		 * @var Auth_Administrator_User $item
		 */
		return $item->getSurname();
	}
	
}
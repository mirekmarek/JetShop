<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ManageAccess\Administrators\Roles;


use Jet\DataListing_Column;
use Jet\Tr;
use JetApplication\Auth_Administrator_Role;

class Listing_Column_ID extends DataListing_Column
{
	public const KEY = 'id';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('ID');
	}
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): string
	{
		/**
		 * @var Auth_Administrator_Role $item
		 */
		return $item->getId();
	}
}
<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ManageAccess\Administrators\Users;


use Jet\DataListing_Column;
use Jet\Tr;

class Listing_Column_UserName extends DataListing_Column
{
	public const KEY = 'username';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Username');
	}
	
}
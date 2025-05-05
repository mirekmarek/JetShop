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

class Listing_Column_Roles extends DataListing_Column
{
	public const KEY = 'roles';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('User roles');
	}
	
	public function getDisallowSorting(): bool
	{
		return true;
	}
	
	public function render( mixed $item ) : string
	{
		/**
		 * @var Auth_Administrator_User $item
		 */
		
		$res = '';
		foreach($item->getRoles() as $role) {
			$res .= $role->getName().'<br>';
		}
		
		return $res;
	}
	
}
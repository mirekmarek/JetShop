<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ManageAccess\Administrators\Users;

use Jet\Tr;
use JetApplication\EMail;
use JetApplication\EMail_Template;
use JetApplication\EShop;

class EMailTemplates_PasswordReset extends EMail_Template
{
	protected string $username = '';
	protected string $name = '';
	protected string $password;
	
	public function setUsername( string $username ): void
	{
		$this->username = $username;
	}
	
	public function setName( string $name ): void
	{
		$this->name = $name;
	}
	
	public function setPassword( string $password ): void
	{
		$this->password = $password;
	}
	
	
	
	protected function init(): void
	{
		$this->setInternalName('Administrators - password reset');
		
		$this->addProperty('username', Tr::_('Username'))
			->setPropertyValueCreator(function() {
				return $this->username;
			});
		$this->addProperty('name', Tr::_('Name'))
			->setPropertyValueCreator(function() {
				return $this->name;
			});
		$this->addProperty('password', Tr::_('Password'))
			->setPropertyValueCreator(function() {
				return $this->password;
			});
	}
	
	public function setupEMail( EShop $eshop, EMail $email ): void
	{
	}
	
	public function initTest( EShop $eshop ): void
	{
		$this->setUsername('pepazdepa@login.nop');
		$this->setName('Pepa Zdepa');
		$this->setPassword('SuperPassword');
	}
}
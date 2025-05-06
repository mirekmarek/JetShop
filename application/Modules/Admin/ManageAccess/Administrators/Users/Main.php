<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ManageAccess\Administrators\Users;


use Jet\Application_Module;
use JetApplication\Auth_Administrator_User;
use JetApplication\EMail_TemplateProvider;
use JetApplication\EShops;


class Main extends Application_Module implements EMail_TemplateProvider
{
	public const ADMIN_MAIN_PAGE = 'administrators-users';
	
	public const ACTION_GET = 'get_user';
	public const ACTION_ADD = 'add_user';
	public const ACTION_UPDATE = 'update_user';
	public const ACTION_DELETE = 'delete_user';
	
	
	public function getEMailTemplates(): array
	{
		return [
			new EMailTemplates_Welcome(),
			new EMailTemplates_PasswordReset()
		];
	}
	
	public function resetPassword( Auth_Administrator_User $user ) : void
	{
		
		$password = Auth_Administrator_User::generatePassword();
		
		$user->setPassword( $password );
		$user->setPasswordIsValid( false );
		$user->save();
		
		
		$email_template = new EMailTemplates_PasswordReset();
		$email_template->setUsername( $user->getUsername() );
		$email_template->setPassword( $password );
		$email_template->setName( $user->getName() );
		
		
		$email = $email_template->createEmail( EShops::getDefault() );
		$email->setTo( $user->getEmail() );
		$email->send();
	}

	public function sendWelcomeEmail( Auth_Administrator_User $user, string $password ): void
	{
		$email_template = new EMailTemplates_Welcome();
		$email_template->setUsername( $user->getUsername() );
		$email_template->setPassword( $password );
		$email_template->setName( $user->getName() );
		
		
		$email = $email_template->createEmail( EShops::getDefault() );
		$email->setTo( $user->getEmail() );
		$email->send();
		
	}
	
}
<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Customer\PasswordReset;


use Jet\Application_Module;
use Jet\Auth;
use Jet\IO_File;
use Jet\Logger;
use Jet\SysConf_Path;
use JetApplication\Customer;
use JetApplication\EShop_Managers_CustomerPasswordReset;
use JetApplication\EShop_ModuleUsingTemplate_Interface;
use JetApplication\EShop_ModuleUsingTemplate_Trait;
use JetApplication\EShop_Pages;
use JetApplication\EMail_TemplateProvider;


class Main extends Application_Module implements EShop_Managers_CustomerPasswordReset, EMail_TemplateProvider, EShop_ModuleUsingTemplate_Interface
{
	use EShop_ModuleUsingTemplate_Trait;
	
	public function generateKey( Customer $user ) : string
	{
		$salt_file = SysConf_Path::getData().'customer_password_reset_salt.txt';
		if(
			!IO_File::exists( $salt_file ) ||
			filectime($salt_file)<strtotime('-1 day') ||
			(!($salt = IO_File::read( $salt_file )))
		) {
			$salt = uniqid().uniqid().uniqid();
			IO_File::write( $salt_file, $salt );
		}
		
		return sha1($user->getId().':'.$user->getEmail().':'.$salt );
	}
	
	public function generateToken( Customer $user ) : string
	{
		$token = PasswordResetToken::generate( $user );
		
		Logger::info(
			event: 'user_password_reset_started',
			event_message: 'User ' . $user->getUsername() . ' (id:' . $user->getId() . ') password reset started',
			context_object_id: $user->getId(),
			context_object_name: $user->getUsername()
		);
		
		$mail_template = new EMailTemplate_Request();
		$mail_template->setCode( $token->getCode() );
		$email = $mail_template->createEmail( $user->getEshop() );
		$email->setSaveHistoryAfterSend( false );
		$email->setContextCustomerId( $user->getId() );
		$email->setTo( $user->getEmail() );
		$email->send();
		
		
		return EShop_Pages::ResetPassword()->getURL(GET_params: ['validate' =>$user->getId(), 'key' =>$this->generateKey($user)]);
		
	}
	
	public function passwordReset(Customer $user, PasswordResetToken $token, string $new_password) : void
	{
		$token->used();
		$user->setPassword( $new_password );
		
		Logger::info(
			event: 'user_password_reset_done',
			event_message: 'User ' . $user->getUsername() . ' (id:' . $user->getId() . ') password reset done',
			context_object_id: $user->getId(),
			context_object_name: $user->getUsername()
		);
		
		$mail_template = new EMailTemplate_Done();
		$email = $mail_template->createEmail( $user->getEshop() );
		$email->setContextCustomerId( $user->getId() );
		$email->setTo( $user->getEmail() );
		$email->send();
		
		Auth::loginUser( $user );
		
	}
	
	public function renderIntegration() : string
	{
		if(Customer::getCurrentCustomer()) {
			return '';
		}
		
		$view = $this->getView();
		
		return
			$view->render('dialog')
			.$view->render('js');
	}
	
	
	public function getEMailTemplates(): array
	{
		$template_request = new EMailTemplate_Request();
		$template_done = new EMailTemplate_Done();
		
		return [
			$template_request,
			$template_done
		];
	}
}
<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Customer\Login;

use Jet\Auth;
use Jet\Factory_MVC;
use Jet\Form_Field;
use Jet\Form_Field_Email;
use Jet\Form_Field_Password;
use Jet\Form;
use Jet\MVC;
use JetApplication\Customer;
use JetApplication\EShop_Managers_CustomerLogin;
use JetApplication\EShop_ModuleUsingTemplate_Interface;
use JetApplication\EShop_ModuleUsingTemplate_Trait;
use JetApplication\EShop_Pages;


class Main extends EShop_Managers_CustomerLogin implements EShop_ModuleUsingTemplate_Interface
{
	use EShop_ModuleUsingTemplate_Trait;
	
	protected static ?Form $login_form = null;

	protected static ?Form $logout_form = null;
	
	public static function getLoginForm( bool $ajax=false ) : Form
	{
		if(!static::$login_form) {
			$email = new Form_Field_Email('email', 'E-mail');
			$email->setIsRequired(true);
			$email->setErrorMessages([
				Form_Field_Email::ERROR_CODE_EMPTY => 'Please enter e-mail',
				Form_Field_Email::ERROR_CODE_INVALID_FORMAT => 'Please enter e-mail'
			]);

			$password = new Form_Field_Password('password', 'Password');
			$password->setIsRequired(true);
			$password->setErrorMessages([
				Form_Field_Password::ERROR_CODE_EMPTY => 'Please enter password',
			]);

			static::$login_form = new Form('customer_login_form', [$email, $password]);

			if($ajax) {
				static::$login_form->setAction(
					EShop_Pages::Login()->getURL(GET_params: ['action' =>'login_ajax'])
				);
			}
			
		}

		return static::$login_form;
	}

	public static function getLogoutForm() : Form
	{
		if(!static::$logout_form) {
			static::$logout_form = new Form('customer_logout_form', []);
			static::$logout_form->setAction(
				EShop_Pages::Login()->getURL(GET_params: ['action' =>'logout'])
			);
		}

		return static::$logout_form;
	}
	
	
	public function handleLogin(): void
	{
		$this->handle('login');
	}
	
	public function handleIsNotActivated( Customer $customer ) : void
	{
		$this->handle('is_not_activated', $customer);
	}
	
	public function handleIsBlocked( Customer $customer ) : void
	{
		$this->handle('is_blocked', $customer);
	}
	
	public function handleMustChangePassword( Customer $customer ) : void
	{
		$this->handle('must_change_password', $customer);
	}
	
	protected function handle( string $action, ?Customer $customer=null ) : void
	{
		$page_content = [];
		$page_content_item = Factory_MVC::getPageContentInstance();
		
		$page_content_item->setModuleName( $this->getModuleManifest()->getName() );
		$page_content_item->setControllerAction( $action );
		
		$page_content[] = $page_content_item;
		
		$page = MVC::getPage();
		
		$page->setContent( $page_content );
		
		echo $page->render();
		
	}
	
	public function renderCustomerIcon() : string
	{
		if(
			MVC::getPage()->getKey()==EShop_Pages::CustomerSection()->getKey() &&
			!Customer::getCurrentCustomer()
		) {
			return '';
		}
		
		return $this->getView()->render('icon');
	}
	
	public function getMustChangePasswordForm() : Form
	{
		$password = new Form_Field_Password( 'password', 'New password: ' );
		$password->setIsRequired( true );
		$password->setErrorMessages(
			[
				Form_Field::ERROR_CODE_EMPTY         => 'Please enter new password',
				Form_Field::ERROR_CODE_WEAK_PASSWORD => 'Password is not strong enough',
				'current_password_used'              => 'Please enter <strong>new</strong> password',
			]
		);
		
		$password->setValidator( function( Form_Field_Password $field ) : bool {
			if(!Customer::verifyPasswordStrength($field->getValue())) {
				$field->setError( Form_Field::ERROR_CODE_WEAK_PASSWORD);
				return false;
			}
			
			if(Auth::getCurrentUser()->verifyPassword($field->getValue())) {
				$field->setError('current_password_used');
				return false;
			}
			
			return true;
		} );
		
		
		$password_check = $password->generateCheckField(
			field_name: 'password_check',
			field_label: 'Confirm new password',
			error_message_empty: 'Please confirm new password',
			error_message_not_match: 'Password confirmation do not match'
		);
		
		
		return new Form(
			'change_password', [
				$password,
				$password_check
			]
		);
	}
	
}
<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Shop\Login;

use Jet\Application_Module;
use Jet\Form_Field_Email;
use Jet\Form_Field_Password;
use Jet\MVC;
use Jet\MVC_Page_Interface;
use Jet\MVC_View;
use JetShop\Customer_AuthController_Interface;
use Jet\Form;

/**
 *
 */
class Main extends Application_Module implements Customer_AuthController_Interface
{

	protected static ?Form $login_form = null;

	protected static ?Form $logout_form = null;

	public static function getLoginPage() : MVC_Page_Interface
	{
		return MVC::getPage('login');
	}

	public static function getLoginForm() : Form
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

			static::$login_form->setAction(static::getLoginPage()->getURL([], ['action'=>'login']));
		}

		return static::$login_form;
	}

	public static function getLogoutForm() : Form
	{
		if(!static::$logout_form) {
			static::$logout_form = new Form('customer_logout_form', []);
			static::$logout_form->setAction(static::getLoginPage()->getURL([], ['action'=>'logout']));
		}

		return static::$logout_form;
	}

	public function renderResetPasswordDialog() : string
	{
		$view = new MVC_View( $this->getViewsDir() );

		return $view->render('js').$view->render('reset_password/dialog');
	}

}
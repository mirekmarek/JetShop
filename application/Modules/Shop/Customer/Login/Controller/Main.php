<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Shop\Customer\Login;

use Jet\AJAX;
use Jet\Auth;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Logger;
use Jet\MVC_Controller_Default;
use Jet\MVC_Controller_Router;
use Jet\MVC_Controller_Router_Interface;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Shop_Pages;
use JetApplication\Customer;

/**
 *
 */
class Controller_Main extends MVC_Controller_Default
{
	protected ?MVC_Controller_Router $router = null;

	public function getControllerRouter(): MVC_Controller_Router_Interface|MVC_Controller_Router|null
	{
		if(!$this->router) {
			$this->router = new MVC_Controller_Router( $this );

			$this->router->setDefaultAction('default');

			$GET = Http_Request::GET();
			$action = $GET->getString('action');


			$this->router->addAction('login_ajax')->setResolver(function() use ($action) {
				return $action=='login_ajax';
			});
			
			$this->router->addAction('login')->setResolver(function() use ($action) {
				return $action=='login';
			});
			

			$this->router->addAction('logout')->setResolver(function() use ($action) {
				return $action=='logout';
			});

		}
		return $this->router;
	}
	
	public function default_Action() : void
	{
		Http_Headers::movedTemporary( Shop_Pages::CustomerSection()->getURL() );
	}
	
	public function login_Action() : void
	{
		$success = false;
		
		$snippets = [];
		
		$form = Main::getLoginForm();
		
		if($form->catch()) {
			$email = $form->field('email')->getValue();
			$password = $form->field('password')->getValue();
			
			if(Auth::login($email, $password)) {
				$success = true;
				Http_Headers::reload();
			} else {
				$form->setCommonMessage( UI_messages::createDanger(Tr::_('Incorrect e-mail or password')) );
			}
		}
		
		$this->view->setVar('login_form', $form);
		
		$this->output('login');
		
	}
	
	
	public function login_ajax_Action() : void
	{
		$success = false;

		$snippets = [];

		$form = Main::getLoginForm( true );

		if($form->catch()) {
			$email = $form->field('email')->getValue();
			$password = $form->field('password')->getValue();

			if(Auth::login($email, $password)) {
				$success = true;
			} else {
				$form->setCommonMessage( UI_messages::createDanger(Tr::_('Incorrect e-mail or password')) );
			}
		}

		if(!$success) {
			$snippets = [
				'customer_login_form_area' => $this->view->render('icon/login/form')
			];
		}

		AJAX::operationResponse( $success, $snippets );
	}

	public function logout_Action() : void
	{
		Auth::logout();

		AJAX::operationResponse(true);
	}
	
	public function is_not_activated_Action(): void
	{
		if(Http_Request::GET()->exists('logout')) {
			Auth::logout();
			Http_Headers::reload(unset_GET_params: ['logout']);
		}
		
		$this->output( 'is-not-activated' );
	}
	
	/**
	 *
	 */
	public function is_blocked_Action(): void
	{
		if(Http_Request::GET()->exists('logout')) {
			Auth::logout();
			Http_Headers::reload(unset_GET_params: ['logout']);
		}
		
		$this->output( 'is-blocked' );
	}
	
	/**
	 *
	 */
	public function must_change_password_Action(): void
	{
		if(Http_Request::GET()->exists('logout')) {
			Auth::logout();
			Http_Headers::reload(unset_GET_params: ['logout']);
		}
		
		/**
		 * @var Main $module
		 */
		$module = $this->getModule();
		
		$form = $module->getMustChangePasswordForm();
		
		if( $form->catchInput() && $form->validate() ) {
			$data = $form->getValues();
			$user = Customer::getCurrentCustomer();
			
			$user->setPassword( $data['password'] );
			$user->setPasswordIsValid( true );
			$user->setPasswordIsValidTill( null );
			$user->save();
			
			Logger::info(
				event: 'password_changed',
				event_message: 'User ' . $user->getUsername() . ' (id:' . $user->getId() . ') changed password',
				context_object_id: $user->getId(),
				context_object_name: $user->getUsername()
			);
			
			Http_Headers::reload();
		}
		
		$this->view->setVar( 'form', $form );
		
		$this->output( 'must-change-password' );
	}
	
}
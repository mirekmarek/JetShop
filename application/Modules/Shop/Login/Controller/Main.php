<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Shop\Login;

use Jet\AJAX;
use Jet\Auth;
use Jet\Http_Request;
use Jet\MVC_Controller_Default;
use Jet\MVC_Controller_Router;
use Jet\MVC_Controller_Router_Interface;
use Jet\Tr;
use Jet\UI_messages;

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


			$this->router->addAction('login')->setResolver(function() use ($action) {
				return $action=='login';
			});

			$this->router->addAction('logout')->setResolver(function() use ($action) {
				return $action=='logout';
			});

		}
		return $this->router;
	}


	/**
	 *
	 */
	public function default_Action() : void
	{
	}

	public function customer_icon_Action() : void
	{
		$this->output('icon');
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
}
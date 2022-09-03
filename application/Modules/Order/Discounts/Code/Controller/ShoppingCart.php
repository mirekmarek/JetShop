<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetShopModule\Order\Discounts\Code;

use Jet\AJAX;
use Jet\Application;
use Jet\Http_Request;
use Jet\MVC_Controller_Default;
use Jet\MVC_Controller_Router;

/**
 *
 */
class Controller_ShoppingCart extends MVC_Controller_Default
{

	public function getControllerRouter(): MVC_Controller_Router
	{
		$router = new MVC_Controller_Router( $this );

		$router->setDefaultAction('default');

		$GET = Http_Request::GET();
		$action = $GET->getString('action');

		$router->addAction('use_code')->setResolver(function() use ($action) {
			return $action=='use_code';
		});

		$router->addAction('cancel_use_code')->setResolver(function() use ($action) {
			return $action=='cancel_use_code';
		});


		return $router;
	}

	/**
	 *
	 */
	public function default_Action() : void
	{
		/**
		 * @var Main $module
		 */
		$module = $this->getModule();

		$this->view->setVar('module', $this->getModule());

		if(($used_code=$module->getUsedCode())) {
			$this->view->setVar('used_code', $used_code);
		}

		$this->output('shopping_cart');

	}

	public function use_code_Action()
	{
		/**
		 * @var Main $module
		 */
		$module = $this->getModule();

		$form = $module->getUseCodeForm();

		$ok = $form->catch();

		$this->view->setVar('module', $this->getModule());

		if(($used_code=$module->getUsedCode())) {
			$this->view->setVar('used_code', $used_code);
			$view = 'shopping_cart/used';
		} else {
			$view = 'shopping_cart/form';
		}

		AJAX::operationResponse($ok, [
			'discount_code_area' => $this->view->render($view)
		]);

	}

	public function cancel_use_code_Action()
	{
		/**
		 * @var Main $module
		 */
		$module = $this->getModule();

		$module->cancelUse();

		$this->view->setVar('module', $this->getModule());

		ob_end_clean();
		echo $this->view->render('shopping_cart/form');
		Application::end();
	}
}
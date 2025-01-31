<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Discounts\Code;


use Jet\AJAX;
use Jet\Http_Request;
use Jet\MVC_Controller_Default;
use Jet\MVC_Controller_Router;
use JetApplication\Discounts_Code;


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

		$this->output('shopping_cart');

	}

	public function use_code_Action() : void
	{
		/**
		 * @var Main $module
		 */
		$module = $this->getModule();
		$this->view->setVar('module', $this->getModule());

		$form = $module->getUseCodeForm();

		$ok = $form->catch();


		AJAX::operationResponse($ok, [
			'discount_code_area' =>
				$this->view->render('shopping_cart/used-codes')
				.$this->view->render('shopping_cart/form')
		]);

	}

	public function cancel_use_code_Action() : void
	{
		/**
		 * @var Main $module
		 */
		$module = $this->getModule();
		$this->view->setVar('module', $this->getModule());
		
		$code = Discounts_Code::getByCode(
			Http_Request::GET()->getString('code')
		);
		
		if( $code ) {
			$module->cancelUse( $code );
		}


		AJAX::snippetResponse(
			$this->view->render('shopping_cart/used-codes')
			.$this->view->render('shopping_cart/form')
		);
	}
}
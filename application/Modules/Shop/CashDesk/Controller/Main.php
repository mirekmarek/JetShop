<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Shop\CashDesk;

use Jet\Http_Headers;
use Jet\Mvc_Controller_Default;
use Jet\Mvc_Controller_Router;
use Jet\Mvc_Controller_Router_Interface;
use Jet\Mvc_View;
use JetShop\ShoppingCart;

/**
 *
 */
class Controller_Main extends Mvc_Controller_Default
{
	use Controller_Main_Delivery;
	use Controller_Main_Payment;
	use Controller_Main_Customer;
	use Controller_Main_Confirm;

	protected ?Mvc_Controller_Router $router = null;

	public function getControllerRouter(): Mvc_Controller_Router_Interface|Mvc_Controller_Router|null
	{
		if(!$this->router) {
			$this->router = new Mvc_Controller_Router( $this );

			$this->router->setDefaultAction('default');

			$this->getControllerRouter_Delivery();
			$this->getControllerRouter_Payment();
			$this->getControllerRouter_Customer();
			$this->getControllerRouter_Confirm();
		}
		return $this->router;
	}

	public function getView() : Mvc_View
	{
		return $this->view;
	}

	public function default_Action() : void
	{
		$cart = ShoppingCart::get();
		if(!$cart->getQuantity()) {
			Http_Headers::movedTemporary(
				ShoppingCart::getCartPage()->getURL()
			);
		}

		$this->output('main');
	}

}



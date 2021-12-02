<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Shop\CashDesk;

use Jet\Http_Headers;
use Jet\MVC;
use Jet\MVC_Controller_Default;
use Jet\MVC_Controller_Router;
use Jet\MVC_Controller_Router_Interface;
use Jet\MVC_View;
use JetShop\CashDesk;
use JetShop\Order;
use JetShop\ShoppingCart;
use JetShop\Shops;

/**
 *
 */
class Controller_Main extends MVC_Controller_Default
{
	use Controller_Main_Delivery;
	use Controller_Main_Payment;
	use Controller_Main_Customer;
	use Controller_Main_Confirm;

	protected ?MVC_Controller_Router $router = null;

	protected ?Order $order = null;

	public function getControllerRouter(): MVC_Controller_Router_Interface|MVC_Controller_Router|null
	{

		if(
			in_array(
				$this->content->getControllerAction(),
				[
					'payment',
					'payment_problem',
					'payment_success',
					'payment_notification',
					'confirmation',
				]
			)
		) {
			$router = MVC::getRouter();
			$key = $router->getUrlPath();

			if( $key ) {
				$this->order = Order::getByKey( $key );
			}

			if(!$this->order) {
				$router->setIsRedirect( Shops::getCurrent()->getHomepage()->getURL() );

				return null;
			}

			$router->setUsedUrlPath( $this->order->getKey() );

			$this->router = new MVC_Controller_Router( $this );
			$this->router->setDefaultAction($this->content->getControllerAction());
			return $this->router;
		}

		if(!$this->router) {
			$this->router = new MVC_Controller_Router( $this );

			$this->router->setDefaultAction('default');

			$this->getControllerRouter_Delivery();
			$this->getControllerRouter_Payment();
			$this->getControllerRouter_Customer();
			$this->getControllerRouter_Confirm();
		}
		return $this->router;
	}

	public function getView() : MVC_View
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


	public function payment_Action() : void
	{
		$payment_method = $this->order->getPaymentMethod();

		$module = $payment_method->getModule();
		if(!$module) {
			Http_Headers::movedTemporary( CashDesk::getCashDeskConfirmationPage()->getURL([$this->order->getKey()]) );
		}

		if($module->handlePayment($this->order)) {
			Http_Headers::movedTemporary( CashDesk::getCashDeskConfirmationPage()->getURL([$this->order->getKey()]) );
		}

	}


	public function payment_problem_Action() : void
	{
		//TODO:
		die();
	}

	public function payment_success_Action() : void
	{
		//TODO:
		die();
	}

	public function payment_notification_Action() : void
	{
		//TODO:
		die();
	}


	public function confirmation_Action() : void
	{
		$this->view->setVar('order', $this->order);

		$this->output('confirmation');
	}


}
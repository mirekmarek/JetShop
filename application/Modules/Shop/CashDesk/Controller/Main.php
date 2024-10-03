<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Shop\CashDesk;

use Jet\Exception;
use Jet\Http_Headers;
use Jet\MVC;
use Jet\MVC_Controller_Default;
use Jet\MVC_Controller_Router;
use Jet\MVC_Controller_Router_Interface;
use Jet\MVC_View;
use JetApplication\Shop_Pages;
use JetApplication\Order;
use JetApplication\Shop_Managers;
use JetApplication\Shops;

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
	protected ?CashDesk $cash_desk = null;

	public function getControllerRouter(): MVC_Controller_Router_Interface|MVC_Controller_Router|null
	{
		$this->cash_desk = $this->module->getCashDesk();
		$this->view->setVar('cash_desk', $this->cash_desk);
		
		if(
			in_array(
				$this->content->getControllerAction(),
				[
					'payment',
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
			
			if(
				!$this->module->getCashDesk()->getDefaultDeliveryMethod() ||
				!$this->module->getCashDesk()->getDefaultPaymentMethod()
			) {
				$this->router = new MVC_Controller_Router( $this );
				
				$this->router->setDefaultAction('error');
				
				return $this->router;
			}
			
			$this->router = new MVC_Controller_Router( $this );

			$this->router->setDefaultAction('default');

			$this->getControllerRouter_Delivery();
			$this->getControllerRouter_Payment();
			$this->getControllerRouter_Customer();
			$this->getControllerRouter_Confirm();
		}
		return $this->router;
	}
	

	public function getCashDesk(): CashDesk
	{
		return $this->cash_desk;
	}
	
	

	public function getView() : MVC_View
	{
		return $this->view;
	}

	public function default_Action() : void
	{
		$cart = Shop_Managers::ShoppingCart()->getCart();
		if(!$cart->getNumberOfUnits()) {
			Http_Headers::movedTemporary(
				Shop_Pages::ShoppingCart()->getURL()
			);
		}

		$this->output('main');
	}


	public function payment_Action() : void
	{
		$payment_method = $this->order->getPaymentMethod();
		
		/**
		 * @var Main $main;
		 */
		$main = $this->module;

		$module = $payment_method->getBackendModule();
		if(!$module) {
			Http_Headers::movedTemporary( Shop_Pages::CashDeskConfirmation()->getURL([$this->order->getKey()]) );
		}
		
		$result = $module->handlePayment($this->order, $payment_method);
		
		$this->view->setVar('order', $this->order );

		if( $result===true ) {
			$this->order->paid();
			
			$this->output('payment-result/paid');
			return;
		}
		
		if($result===false) {
			$this->output('payment-result/not-paid');
			return;
		}
		
		$this->output('payment-result/error');
		
	}

	public function error_Action() : void
	{
		if(!Shop_Managers::ShoppingCart()->getCart()->getNumberOfUnits()) {
			Http_Headers::movedTemporary( Shop_Pages::ShoppingCart()->getURL() );
		}

		throw new Exception('Oder system configuration problem');
	}



	public function confirmation_Action() : void
	{
		$this->view->setVar('order', $this->order);

		$this->output('confirmation');
	}


}
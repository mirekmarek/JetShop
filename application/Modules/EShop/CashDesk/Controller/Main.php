<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\EShop\CashDesk;

use Jet\Exception;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\MVC;
use Jet\MVC_Controller_Default;
use Jet\MVC_Controller_Router;
use Jet\MVC_Controller_Router_Interface;
use Jet\MVC_View;
use JetApplication\EShop_Pages;
use JetApplication\Order;
use JetApplication\EShop_Managers;
use JetApplication\EShops;

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
				$router->setIsRedirect( EShops::getCurrent()->getHomepage()->getURL() );

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
		$cart = EShop_Managers::ShoppingCart()->getCart();
		if(!$cart->getNumberOfUnits()) {
			Http_Headers::movedTemporary(
				EShop_Pages::ShoppingCart()->getURL()
			);
		}

		$this->output('main');
	}


	public function payment_Action() : void
	{
		/**
		 * @var Main $main;
		 */
		$main = $this->module;
		
		$payment_method = $this->order->getPaymentMethod();
		$module = $payment_method->getBackendModule();
		if(!$module) {
			Http_Headers::movedTemporary( EShop_Pages::CashDeskConfirmation()->getURL([$this->order->getKey()]) );
		}
		
		if(!$this->order->getPaid()) {
			
			$action = Http_Request::GET()->getString( 'a' );
			
			$this->view->setVar('order', $this->order );
			
			$return_url = EShop_Pages::CashDeskPayment()->getURL([$this->order->getKey()],['a'=>'handle_return']);
			
			switch( $action ) {
				case 'handle_return':
					if(!$module->handlePaymentReturn( $this->order )) {
						$this->output('payment-result/error');
						
						return;
					}
					break;
				case 'try_again':
				default:
					if(!$module->handlePayment($this->order, $return_url)) {
						$this->output('payment-result/error');
						
						return;
					}
					break;
			}
		}
		
		
		if($this->order->getPaid()) {
			$this->output('payment-result/paid');
		} else {
			$this->view->setVar( 'try_again_url', Http_Request::currentURI(set_GET_params: ['a'=>'try_again']) );
			$this->output('payment-result/not-paid');
		}
		
	}

	public function error_Action() : void
	{
		if(!EShop_Managers::ShoppingCart()->getCart()->getNumberOfUnits()) {
			Http_Headers::movedTemporary( EShop_Pages::ShoppingCart()->getURL() );
		}

		throw new Exception('Oder system configuration problem');
	}



	public function confirmation_Action() : void
	{
		$this->view->setVar('order', $this->order);

		$this->output('confirmation');
	}


}
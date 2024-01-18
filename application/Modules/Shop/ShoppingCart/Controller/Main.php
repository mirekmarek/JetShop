<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Shop\ShoppingCart;

use Jet\AJAX;
use Jet\Http_Request;
use Jet\MVC_Controller_Default;
use Jet\MVC_Controller_Router;
use Jet\MVC_Controller_Router_Interface;
use JetApplication\Shop_Managers;
use JetApplication\Shop_Managers_ShoppingCart;
use JetApplication\ShoppingCart;

/**
 *
 */
class Controller_Main extends MVC_Controller_Default
{
	protected ?MVC_Controller_Router $router = null;
	
	protected ShoppingCart $cart;
	protected Shop_Managers_ShoppingCart $manager;

	public function getControllerRouter(): MVC_Controller_Router_Interface|MVC_Controller_Router|null
	{
		if(!$this->router) {
			$this->manager = Shop_Managers::ShoppingCart();
			$this->cart = $this->manager->getCart();
			
			$this->router = new MVC_Controller_Router( $this );

			$this->router->setDefaultAction('default');

			$GET = Http_Request::GET();

			$this->router->addAction('buy')->setResolver( function() use ($GET) {
				return $GET->exists('buy');
			} );

			$this->router->addAction('set_qty')->setResolver( function() use ($GET) {
				return $GET->exists('set_qty');
			} );

			$this->router->addAction('remove')->setResolver( function() use ($GET) {
				return $GET->exists('remove');
			} );
		}

		return $this->router;
	}

	/**
	 *
	 */
	public function default_Action() : void
	{
		$this->output('shopping_cart_page');
	}

	public function cart_icon_Action(): void
	{
		$this->output('icon');
	}

	public function cart_popup_dialog_Action(): void
	{
		$this->output('popup_dialog');
	}

	public function buy_Action() : void
	{
		$GET = Http_Request::GET();

		$product_id = $GET->getInt('buy');
		$product_gty = $GET->getInt('gty');
		

		$error_message = '';
		if(($new_item = $this->cart->addItem( $product_id, $product_gty, $error_message ))) {
			$this->manager->saveCart();
			
			$this->view->setVar('new_item', $new_item);

			AJAX::commonResponse([
				'ok' => true,
				'snippets' => [
					'shopping_cart_popup_content' => $this->view->render('popup_content'),
					'shopping_cart_icon' => $this->view->render('icon'),
				]
			]);
		}

		AJAX::commonResponse([
			'ok' => false,
			'error_message' => $error_message
		]);
	}

	public function remove_Action() : void
	{
		$GET = Http_Request::GET();

		$this->cart->removeItem( $GET->getInt('remove') );
		$this->manager->saveCart();

		AJAX::commonResponse([
			'ok' => true,
			'snippets' => [
				'shopping_cart' => $this->view->render('shopping_cart_page/shopping_cart')
			]
		]);
	}

	public function set_qty_Action() : void
	{
		$GET = Http_Request::GET();
		
		$this->cart->setQuantity( $GET->getInt('set_qty'), $GET->getInt('gty') );
		$this->manager->saveCart();

		AJAX::commonResponse([
			'ok' => true,
			'snippets' => [
				'shopping_cart' => $this->view->render('shopping_cart_page/shopping_cart')
			]
		]);
	}

}
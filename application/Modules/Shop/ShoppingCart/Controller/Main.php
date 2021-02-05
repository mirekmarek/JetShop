<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Shop\ShoppingCart;

use Jet\AJAX;
use Jet\Http_Request;
use Jet\Mvc_Controller_Default;
use Jet\Mvc_Controller_Router;
use Jet\Mvc_Controller_Router_Interface;
use JetShop\ShoppingCart;

/**
 *
 */
class Controller_Main extends Mvc_Controller_Default
{
	protected ?Mvc_Controller_Router $router = null;

	public function getControllerRouter(): Mvc_Controller_Router_Interface|Mvc_Controller_Router|null
	{
		if(!$this->router) {
			$this->router = new Mvc_Controller_Router( $this );

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

		$cart = ShoppingCart::get();

		$error_message = '';
		if(($new_item = $cart->addItem( $product_id, $product_gty, $error_message ))) {
			$this->view->setVar('new_item', $new_item);

			AJAX::response([
				'ok' => true,
				'snippets' => [
					'shopping_cart_popup_content' => $this->view->render('popup_content'),
					'shopping_cart_icon' => $this->view->render('icon'),
				]
			]);
		}

		AJAX::response([
			'ok' => false,
			'error_message' => $error_message
		]);
	}

	public function remove_Action() : void
	{
		$GET = Http_Request::GET();

		$cart = ShoppingCart::get();

		$cart->removeItem( $GET->getInt('remove') );

		AJAX::response([
			'ok' => true,
			'snippets' => [
				'shopping_cart' => $this->view->render('shopping_cart_page/items')
			]
		]);
	}

	public function set_qty_Action() : void
	{
		$GET = Http_Request::GET();

		$cart = ShoppingCart::get();

		$cart->setQuantity( $GET->getInt('set_qty'), $GET->getInt('gty') );

		AJAX::response([
			'ok' => true,
			'snippets' => [
				'shopping_cart' => $this->view->render('shopping_cart_page/items')
			]
		]);
	}

}
<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\ShoppingCart;


use Jet\AJAX;
use Jet\Http_Request;
use Jet\MVC_Controller_Default;
use Jet\MVC_Controller_Router;
use Jet\MVC_Controller_Router_Interface;
use JetApplication\Marketing_AutoOffer;
use JetApplication\Product_EShopData;
use JetApplication\Application_Service_EShop;
use JetApplication\Application_Service_EShop_ShoppingCart;
use JetApplication\ShoppingCart;


class Controller_Main extends MVC_Controller_Default
{
	protected ?MVC_Controller_Router $router = null;
	
	protected ShoppingCart $cart;
	protected Application_Service_EShop_ShoppingCart $manager;

	public function getControllerRouter(): MVC_Controller_Router_Interface|MVC_Controller_Router|null
	{
		if(!$this->router) {
			$this->manager = Application_Service_EShop::ShoppingCart();
			$this->cart = $this->manager->getCart();
			$this->view->setVar('cart', $this->cart);
			$this->view->setVar('cash_desk', Application_Service_EShop::CashDesk()->getCashDesk() );
			
			$this->router = new MVC_Controller_Router( $this );

			$this->router->setDefaultAction('default');

			$GET = Http_Request::GET();

			$this->router->addAction('buy')->setResolver( function() use ($GET) {
				return $GET->exists('buy');
			} );
			
			$this->router->addAction('select_auto_offer')->setResolver( function() use ($GET) {
				return $GET->exists('select_auto_offer');
			} );
			

			$this->router->addAction('set_qty')->setResolver( function() use ($GET) {
				return $GET->exists('set_qty');
			} );

			$this->router->addAction('remove')->setResolver( function() use ($GET) {
				return $GET->exists('remove');
			} );
			
			$this->router->addAction('select_gift')->setResolver( function() use ($GET) {
				return $GET->exists('select_gift');
			} );
			
			$this->router->addAction('unselect_gift')->setResolver( function() use ($GET) {
				return $GET->exists('unselect_gift');
			} );
			
			$this->router->addAction('select_product_gift')->setResolver( function() use ($GET) {
				return $GET->exists('select_product_gift');
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
	
	public function select_auto_offer_Action() : void
	{
		$GET = Http_Request::GET();
		
		$auto_offer_id = $GET->getInt('select_auto_offer');
		$product_gty = $GET->getInt('qty');
		$variant_id  = $GET->getInt('variant');
		
		$auto_offer = Marketing_AutoOffer::load( $auto_offer_id );
		if(!$auto_offer) {
			return;
		}
		
		
		$error_message = '';
		if(($new_item = $this->cart->selectAutoOffer( $auto_offer, $variant_id, $product_gty, error_message: $error_message ))) {
			$this->manager->saveCart();
			
			AJAX::commonResponse([
				'ok' => true,
				'snippets' => [
					'shopping-cart' => $this->view->render('shopping_cart_page/shopping_cart')
				]
			]);
		}
		
		AJAX::commonResponse([
			'ok' => false,
			'error_message' => $error_message
		]);
	}
	
	
	public function buy_Action() : void
	{
		$GET = Http_Request::GET();

		$product_id = $GET->getInt('buy');
		$product_gty = $GET->getFloat('gty');
		$gift_id = $GET->getInt('gift');
		
		
		$product = Product_EShopData::get( $product_id );
		if(!$product) {
			return;
		}

		$error_message = '';
		if(($new_item = $this->cart->addItem( $product, number_of_units: $product_gty, selected_gift_id: $gift_id, error_message: $error_message ))) {
			$this->manager->saveCart();
			
			$this->view->setVar('new_item', $new_item);
			$this->view->setVar('cart', $this->cart);

			AJAX::commonResponse([
				'ok' => true,
				'error_message' => $error_message,
				'snippets' => [
					'shopping_cart_popup_content' => $this->view->render('popup_dialog/content'),
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

		$removed = $this->cart->removeItem( $GET->getInt('remove') );
		$this->manager->saveCart();
		
		$snippet = $this->view->render('shopping_cart_page/shopping_cart');
		if($removed) {
			$snippet .= Application_Service_EShop::AnalyticsManager()?->removeFromCart( $removed );
		}

		AJAX::commonResponse([
			'ok' => true,
			'snippets' => [
				'shopping-cart' => $snippet
			]
		]);
	}

	public function set_qty_Action() : void
	{
		$GET = Http_Request::GET();
		
		$error_message = '';
		$ok = $this->cart->setNumberOfUnits( $GET->getInt('set_qty'), $GET->getFloat('gty'), $error_message );

		$this->manager->saveCart();

		AJAX::commonResponse([
			'ok' => $ok,
			'error_message' => $error_message,
			'snippets' => [
				'shopping-cart' => $this->view->render('shopping_cart_page/shopping_cart')
			]
		]);
	}
	
	public function select_gift_Action() : void
	{
		$GET = Http_Request::GET();
		
		$this->cart->selectCartGift( $GET->getInt('select_gift') );
		$this->manager->saveCart();
		
		AJAX::commonResponse([
			'ok' => true,
			'snippets' => [
				'shopping-cart' => $this->view->render('shopping_cart_page/shopping_cart')
			]
		]);
		
	}
	
	public function unselect_gift_Action() : void
	{
		$GET = Http_Request::GET();
		
		$this->cart->unselectCartGift( $GET->getInt('unselect_gift') );
		$this->manager->saveCart();
		
		AJAX::commonResponse([
			'ok' => true,
			'snippets' => [
				'shopping-cart' => $this->view->render('shopping_cart_page/shopping_cart')
			]
		]);
	}

	public function select_product_gift_Action() : void
	{
		$GET = Http_Request::GET();
		
		$this->cart->selectProductGift( $GET->getInt('product_id'), $GET->getInt('select_product_gift') );
		$this->manager->saveCart();
		
		AJAX::commonResponse([
			'ok' => true
		]);
		
	}

}
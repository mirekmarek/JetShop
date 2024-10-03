<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Shop\ShoppingCart;

use Jet\Application_Module;
use JetApplication\Availabilities;
use JetApplication\Pricelists;
use JetApplication\Product_ShopData;
use JetApplication\Shop_Managers_ShoppingCart;
use JetApplication\Shop_ModuleUsingTemplate_Interface;
use JetApplication\Shop_ModuleUsingTemplate_Trait;
use JetApplication\ShoppingCart;
use JetApplication\Shops;

/**
 *
 */
class Main extends Application_Module implements Shop_Managers_ShoppingCart, Shop_ModuleUsingTemplate_Interface
{
	use Shop_ModuleUsingTemplate_Trait;
	
	protected static ?ShoppingCart $cart = null;
	
	public function getCart(): ShoppingCart
	{
		if(!static::$cart) {
			if(!isset($_COOKIE['cart_id'])) {
				$_COOKIE['cart_id'] = uniqid().uniqid().uniqid();
				setcookie(
					'cart_id',
					$_COOKIE['cart_id'],
					time() + (10 * 365 * 24 * 60 * 60),
					'/'
				);
			}
			
			static::$cart = new ShoppingCart(
				Shops::getCurrent(),
				Availabilities::getCurrent(),
				Pricelists::getCurrent()
			);
			
			static::$cart->setId( $_COOKIE['cart_id'] );
			
			$this->loadCart();
		}
		
		return static::$cart;
	}
	
	protected function loadCart() : void
	{
		Storage::loadCart( $this->getCart() );
	}
	
	public function saveCart() : void
	{
		Storage::saveCart( $this->getCart() );
	}
	
	public function resetCart(): void
	{
		$this->getCart()->reset();
		$this->saveCart();
	}
	
	
	public function renderIntegration() : string
	{
		$view = $this->getView();
		return
			$view->render('popup_dialog').
			$view->render('js');
	}
	
	public function renderIcon() : string
	{
		$view = $this->getView();
		return $view->render('icon');
	}
	
	
	public function renderBuyButton_listing( Product_ShopData $product ) : string
	{
		$view = $this->getView();
		$view->setVar('product', $product);
		$view->setVar('cart', $this->getCart());
		return $view->render('buy-button/listing');
	}
	
	public function renderBuyButton_detail( Product_ShopData $product ) : string
	{
		$view = $this->getView();
		$view->setVar('product', $product);
		$view->setVar('cart', $this->getCart());
		return $view->render('buy-button/detail');
	}
	
	
}


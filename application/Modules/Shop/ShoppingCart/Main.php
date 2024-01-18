<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Shop\ShoppingCart;

use Jet\Application_Module;
use Jet\MVC;
use Jet\MVC_Page_Interface;
use JetApplication\Shop_Managers_ShoppingCart;
use JetApplication\ShoppingCart;
use JetApplication\Shops;

/**
 *
 */
class Main extends Application_Module implements Shop_Managers_ShoppingCart
{
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
			
			static::$cart = new ShoppingCart( Shops::getCurrent() );
			static::$cart->setId( $_COOKIE['cart_id'] );
			
			$this->loadCart();
		}
		
		return static::$cart;
	}
	
	
	public function getCartPageId(): string
	{
		return 'shopping-cart';
	}
	
	public function getCartPage(): MVC_Page_Interface
	{
		$shop = Shops::getCurrent();
		
		return MVC::getPage($this->getCartPageId(), $shop->getLocale(), $shop->getBaseId());
	}
	
	public function getCartPageURL(): string
	{
		return $this->getCartPage()->getURL();
	}
	
	protected function loadCart() : void
	{
		Storage::loadCart( $this->getCart() );
		
	}
	
	public function saveCart() : void
	{
		Storage::saveCart( $this->getCart() );
	}
	
}


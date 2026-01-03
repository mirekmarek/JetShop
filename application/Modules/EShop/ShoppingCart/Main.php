<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\ShoppingCart;

use Jet\Tr;
use JetApplication\Availabilities;
use JetApplication\Pricelists;
use JetApplication\Product_EShopData;
use JetApplication\Application_Service_EShop_ShoppingCart;
use JetApplication\EShop_ModuleUsingTemplate_Interface;
use JetApplication\EShop_ModuleUsingTemplate_Trait;
use JetApplication\ShoppingCart;
use JetApplication\EShops;

class Main extends Application_Service_EShop_ShoppingCart implements EShop_ModuleUsingTemplate_Interface
{
	use EShop_ModuleUsingTemplate_Trait;
	
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
				EShops::getCurrent(),
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
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() {
				$view = $this->getView();
				return
					$view->render('popup_dialog').
					$view->render('js');
				
			}
		);
		
	}
	
	public function renderIcon() : string
	{
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() {
				$view = $this->getView();
				$view->setVar('cart', $this->getCart());
				return $view->render('icon');
			}
		);
		
	}
	
	
	public function renderBuyButton_listing( Product_EShopData $product ) : string
	{
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() use ($product) {
				$view = $this->getView();
				$view->setVar('product', $product);
				$view->setVar('cart', $this->getCart());
				return $view->render('buy-button/listing');
			}
		);
		
	}
	
	public function renderBuyButton_detail( Product_EShopData $product ) : string
	{
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() use ($product) {
				$view = $this->getView();
				$view->setVar('product', $product);
				$view->setVar('cart', $this->getCart());
				return $view->render('buy-button/detail');
			}
		);
	}
	
	
}


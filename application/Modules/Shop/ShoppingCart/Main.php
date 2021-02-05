<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Shop\ShoppingCart;

use Jet\Application_Module;
use JetShop\Shop_Module_Trait;

/**
 *
 */
class Main extends Application_Module
{
	use Shop_Module_Trait;

	/**
	 *
	 * @return string
	 */
	public function getViewsDir(): string
	{
		return $this->_getViewsDir('shopping_cart');
	}

}
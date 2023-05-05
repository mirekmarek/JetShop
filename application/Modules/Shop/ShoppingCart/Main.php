<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Shop\ShoppingCart;

use Jet\Application_Module;
use JetApplication\Shop_Module_Trait;

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
		return parent::getViewsDir();
		//return $this->_getViewsDir('shopping_cart');
	}

}
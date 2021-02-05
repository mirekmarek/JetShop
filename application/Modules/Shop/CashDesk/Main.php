<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Shop\CashDesk;

use JetShop\CashDesk;
use JetShop\CashDesk_Module;
use JetShop\Delivery_Method;
use JetShop\Shop_Module_Trait;

/**
 *
 */
class Main extends CashDesk_Module
{
	use Shop_Module_Trait;

	/**
	 *
	 * @return string
	 */
	public function getViewsDir(): string
	{
		return $this->_getViewsDir('cash_desk');
	}

	public function sortDeliveryMethods( CashDesk $cash_desk, iterable &$delivery_methods ) : void
	{
		//TODO:
	}

	public function getDefaultDeliveryMethod( CashDesk $cash_desk, iterable &$delivery_methods ) : ?Delivery_Method
	{
		//TODO:
		foreach($delivery_methods as $delivery_method) {
			return $delivery_method;
		}

		return null;
	}
}
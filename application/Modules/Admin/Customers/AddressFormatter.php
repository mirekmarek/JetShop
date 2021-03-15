<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetShopModule\Admin\Customers;


use Jet\Application_Modules;
use Jet\Mvc_View;
use JetShop\Customer_Address;

/**
 *
 */
class AddressFormatter
{

	public static function format( string $shop_code, Customer_Address $address ) : string
	{
		$module = Application_Modules::moduleInstance('Admin.Customers');
		$view = new Mvc_View($module->getViewsDir());

		$view->setVar('address', $address);
		$view->setVar('shop_code', $shop_code);

		return $view->render('address/default');
	}

}
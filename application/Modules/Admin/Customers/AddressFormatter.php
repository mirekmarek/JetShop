<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Customers;


use Jet\Application_Modules;
use Jet\MVC_View;
use JetApplication\Customer_Address;
use JetApplication\Shops_Shop;

/**
 *
 */
class AddressFormatter
{

	public static function format( Shops_Shop $shop, Customer_Address $address ) : string
	{
		$module = Application_Modules::moduleInstance('Admin.Customers');
		$view = new MVC_View($module->getViewsDir());

		$view->setVar('address', $address);
		$view->setVar('shop', $shop);

		return $view->render('address/default');
	}

}
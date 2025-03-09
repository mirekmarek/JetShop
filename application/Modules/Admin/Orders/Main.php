<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;


use Jet\Factory_MVC;
use Jet\Tr;
use JetApplication\Customer;
use JetApplication\Admin_Managers_Order;
use JetApplication\EShopEntity_Basic;
use JetApplication\Order;


class Main extends Admin_Managers_Order
{
	public const ADMIN_MAIN_PAGE = 'orders';
	
	public static function getCurrentUserCanCreate(): bool
	{
		return false;
	}
	
	public static function getCurrentUserCanDelete(): bool
	{
		return false;
	}
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Order();
	}
	
	public function showOrdersOfCustomer( Customer $customer ) : string
	{
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() use ($customer) : string
			{
				$orders = Order::fetchInstances(['customer_id'=>$customer->getId()]);
				$orders->getQuery()->setOrderBy(['-id']);
				
				$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
				$view->setVar('orders', $orders);
				
				return $view->render('customer_orders');
				
			}
		);
		
	}
	
}
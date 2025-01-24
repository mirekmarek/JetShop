<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Orders;

use Jet\Application_Module;
use Jet\Factory_MVC;
use Jet\Tr;
use JetApplication\Admin_Entity_WithEShopRelation_Interface;
use JetApplication\Admin_EntityManager_WithEShopRelation_Trait;
use JetApplication\Customer;
use JetApplication\Admin_Managers_Order;
use JetApplication\Entity_WithEShopRelation;
use JetApplication\Order;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_Order
{
	use Admin_EntityManager_WithEShopRelation_Trait;
	
	public const ADMIN_MAIN_PAGE = 'orders';

	public const ACTION_GET = 'get_order';
	public const ACTION_UPDATE = 'update_order';
	
	public static function getCurrentUserCanCreate(): bool
	{
		return false;
	}
	
	public static function getCurrentUserCanDelete(): bool
	{
		return false;
	}
	
	public static function getEntityInstance(): Entity_WithEShopRelation|Admin_Entity_WithEShopRelation_Interface
	{
		return new Order();
	}
	
	public static function getEntityNameReadable(): string
	{
		return 'Order';
	}
	
	public function showOrderStatus( Order $order ): string
	{
		
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() use ($order) {
				$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
				$view->setVar('order', $order );
				
				return $view->render('order_status');
			}
		);
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
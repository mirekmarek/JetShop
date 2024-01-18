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
use JetApplication\Admin_Entity_Common_Interface;
use JetApplication\Admin_Entity_Common_Manager_Interface;
use JetApplication\Admin_Entity_Common_Manager_Trait;
use JetApplication\Customer;
use JetApplication\Entity_Basic;
use JetApplication\Admin_Managers_Order;
use JetApplication\Order_Status_ShopData;
use JetApplication\Shops_Shop;

/**
 *
 */
class Main extends Application_Module implements Admin_Entity_Common_Manager_Interface, Admin_Managers_Order
{
	use Admin_Entity_Common_Manager_Trait;
	
	public const ADMIN_MAIN_PAGE = 'orders';

	public const ACTION_GET = 'get_order';
	public const ACTION_UPDATE = 'update_order';
	
	public static function getName( int $id ): string
	{
		return Order::get($id)?->getAdminTitle();
	}
	
	public static function showName( int $id ): string
	{
		$title = Order::get($id)?->getAdminTitle();
		
		return '<a href="'.static::getEditUrl($id).'">'.$title.'</a>';
	}
	
	public static function showActiveState( int $id ): string
	{
		return '';
	}
	
	public static function getCurrentUserCanEdit(): bool
	{
		return false;
	}
	
	public static function getCurrentUserCanCreate(): bool
	{
		return false;
	}
	
	public static function getCurrentUserCanDelete(): bool
	{
		return false;
	}
	
	public static function getEntityInstance(): Entity_Basic|Admin_Entity_Common_Interface
	{
		return new Order();
	}
	
	public static function getEntityNameReadable(): string
	{
		return 'customer';
	}
	
	public function showOrderStatus( Shops_Shop $shop, int $status_id ): string
	{
		$status = Order_Status_ShopData::get( $status_id, $shop );
		if(!$status) {
			return '';
		}
		
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		$view->setVar('status', $status);
		
		return $view->render('order_status');
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
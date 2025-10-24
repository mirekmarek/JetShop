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
use JetApplication\Application_Service_Admin_Order;
use JetApplication\EShopEntity_Basic;
use JetApplication\Order;
use JetApplication\SysServices_Definition;
use JetApplication\SysServices_Provider_Interface;
use Jet\Data_DateTime;


class Main extends Application_Service_Admin_Order implements SysServices_Provider_Interface
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
	
	public function getSysServicesDefinitions(): array
	{
		$archive_orders = new SysServices_Definition(
			module: $this,
			name: 'Archive orders',
			description: 'Archive orders older than 3 years',
			service_code: 'archive_orders',
			service: function() {
				Order::updateData(
					data: [
						'archived' => true,
					],
					where: [
						'archived' => false,
						'AND',
						'date_purchased <=' => new Data_DateTime( date('Y-m-d H:i:s', strtotime('-3 years')) ),
					]
				);
				
			}
		);
		
		return [$archive_orders];
	}
}
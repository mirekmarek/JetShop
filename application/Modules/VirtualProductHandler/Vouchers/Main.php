<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\VirtualProductHandler\Vouchers;

use JetApplication\Order;
use JetApplication\Order_ProductOverviewItem;
use JetApplication\Product_VirtualProductHandler;

/**
 *
 */
class Main extends Product_VirtualProductHandler
{
	
	public function dispatchOrder( Order $order, Order_ProductOverviewItem $item ) : void
	{
		// TODO: Implement dispatchOrder() method.
	}
}
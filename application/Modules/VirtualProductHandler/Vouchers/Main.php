<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\VirtualProductHandler\Vouchers;


use JetApplication\Order;
use JetApplication\Order_ProductOverviewItem;
use JetApplication\Product_VirtualProductHandler;


class Main extends Product_VirtualProductHandler
{
	
	public function dispatchOrder( Order $order, Order_ProductOverviewItem $item ) : void
	{
		// TODO: Implement dispatchOrder() method.
	}
}
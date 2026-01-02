<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\VirtualProductHandler\EBooks;


use JetApplication\Order;
use JetApplication\Order_Item;
use JetApplication\Order_Item_SetItem;
use JetApplication\Product_VirtualProductHandler;


class Main extends Product_VirtualProductHandler
{
	
	public function dispatchOrder( Order $order, Order_Item|Order_Item_SetItem $item ) : void
	{
		// TODO: Implement dispatchOrder() method.
	}
}
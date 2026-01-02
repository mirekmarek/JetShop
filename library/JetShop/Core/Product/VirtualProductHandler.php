<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use JetApplication\Order;
use JetApplication\Order_Item;
use JetApplication\Order_Item_SetItem;

abstract class Core_Product_VirtualProductHandler extends Application_Module {
	abstract public function dispatchOrder( Order $order, Order_Item|Order_Item_SetItem $item ) : void;
}
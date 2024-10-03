<?php
namespace JetShop;

use Jet\Application_Module;
use JetApplication\Order;
use JetApplication\Order_ProductOverviewItem;

abstract class Core_Product_VirtualProductHandler extends Application_Module {
	abstract public function dispatchOrder( Order $order, Order_ProductOverviewItem $item ) : void;
}
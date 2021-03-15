<?php
namespace JetShop;

use Jet\Application_Module;

abstract class Core_WarehouseManagement_ManageModule extends Application_Module {

	abstract public function selectWarehousesForOrder( Order $order ) : void;

	abstract public function recalculateProductAvailability( int $product_id ) : void;
}
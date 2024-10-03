<?php
/**
 *
 */

namespace JetShop;

use Jet\Application_Module;

use JetApplication\Supplier_GoodsOrder;

abstract class Core_Supplier_Backend_Module extends Application_Module
{
	public abstract function sendOrder( Supplier_GoodsOrder $order, &$error_message ) : bool;
}
<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;

use JetApplication\Supplier_GoodsOrder;

abstract class Core_Supplier_Backend_Module extends Application_Module
{
	public abstract function sendOrder( Supplier_GoodsOrder $order, string &$error_message ) : bool;
}
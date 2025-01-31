<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Admin_EntityManager_Module;
use JetApplication\Order;
use JetApplication\Customer;

abstract class Core_Admin_Managers_Order extends Admin_EntityManager_Module
{
	abstract public function showOrderStatus( Order $order ) : string;
	
	abstract public function showOrdersOfCustomer( Customer $customer ) : string;
}
<?php
namespace JetShop;

use Jet\Application_Module;

abstract class Core_CashDesk_Module extends Application_Module {


	abstract public function sortDeliveryMethods( CashDesk $cash_desk, iterable &$delivery_methods ) : void;

	abstract public function getDefaultDeliveryMethod( CashDesk $cash_desk, iterable &$delivery_methods ) : ?Delivery_Method;

}
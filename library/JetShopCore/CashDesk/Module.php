<?php
namespace JetShop;

use Jet\Application_Module;
use Jet\Form;

abstract class Core_CashDesk_Module extends Application_Module {


	abstract public function sortDeliveryMethods( CashDesk $cash_desk, array &$delivery_methods ) : void;

	abstract public function getDefaultDeliveryMethod( CashDesk $cash_desk, array $delivery_methods ) : ?Delivery_Method;

	abstract public function sortPaymentMethods( CashDesk $cash_desk, array &$payment_methods ) : void;

	abstract public function getDefaultPaymentMethod( CashDesk $cash_desk, array $payment_methods ) : ?Payment_Method;

	abstract public function updateBillingAddressForm( CashDesk $cash_desk, Form $form ) : void;

	abstract public function updateDeliveryAddressForm( CashDesk $cash_desk, Form $form ) : void;

	abstract public function initAgreeFlags( CashDesk $cash_desk, array &$agree_flags ) : void;
}
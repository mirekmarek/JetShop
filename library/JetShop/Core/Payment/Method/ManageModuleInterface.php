<?php
namespace JetShop;

interface Core_Payment_Method_ManageModuleInterface {

	public function getPaymentMethodEditURL( string $id ) : string;

	public static function getCurrentUserCanEditPaymentMethod() : bool;

	public static function getCurrentUserCanCreatePaymentMethod() : bool;

}
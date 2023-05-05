<?php
namespace JetShop;

interface Core_Delivery_Method_ManageModuleInterface {

	public function getDeliveryMethodEditURL( string $id ) : string;

	public static function getCurrentUserCanEditDeliveryMethod() : bool;

	public static function getCurrentUserCanCreateDeliveryMethod() : bool;

}
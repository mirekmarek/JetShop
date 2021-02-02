<?php
namespace JetShop;

interface Core_Delivery_Class_ManageModuleInterface {

	public function getDeliveryClassEditURL( string $id ) : string;

	public static function getCurrentUserCanEditDeliveryClass() : bool;

	public static function getCurrentUserCanCreateDeliveryClass() : bool;

}
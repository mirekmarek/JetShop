<?php
namespace JetShop;

interface Core_Delivery_Deadline_ManageModuleInterface {

	public function getDeliveryTermEditUrl( string $code ) : string;

	public static function getCurrentUserCanEditDeliveryTerm() : bool;

	public static function getCurrentUserCanCreateDeliveryTerm() : bool;
}
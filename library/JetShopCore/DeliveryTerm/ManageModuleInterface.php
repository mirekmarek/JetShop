<?php
namespace JetShop;

interface Core_DeliveryTerm_ManageModuleInterface {

	public function getDeliveryTermEditUrl( int $id ) : string;

	public static function getCurrentUserCanEditDeliveryTerm() : bool;

	public static function getCurrentUserCanCreateDeliveryTerm() : bool;
}
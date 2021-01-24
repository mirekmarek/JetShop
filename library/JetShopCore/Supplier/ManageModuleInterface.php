<?php
namespace JetShop;

interface Core_Supplier_ManageModuleInterface {

	public function getSupplierEditUrl( int $id ) : string;

	public static function getCurrentUserCanEditSupplier() : bool;

	public static function getCurrentUserCanCreateSupplier() : bool;
}
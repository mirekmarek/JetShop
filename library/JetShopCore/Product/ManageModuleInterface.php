<?php
namespace JetShop;

interface Core_Product_ManageModuleInterface {

	public function getProductSelectWhispererUrl( array $only_types=[], bool $only_active=false ) : string;

	public function getProductEditUrl( int $id ) : string;

	public static function getCurrentUserCanEditProduct() : bool;

	public static function getCurrentUserCanCreateProduct() : bool;

}
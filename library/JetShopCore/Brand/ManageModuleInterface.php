<?php
namespace JetShop;

interface Core_Brand_ManageModuleInterface {

	public function getBrandEditUrl( int $id ) : string;

	public static function getCurrentUserCanEditBrand() : bool;

	public static function getCurrentUserCanCreateBrand() : bool;
}
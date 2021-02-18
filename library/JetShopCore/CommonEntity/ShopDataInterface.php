<?php
namespace JetShop;

interface Core_CommonEntity_ShopDataInterface {

	public function getShopCode() : string;

	public function setShopCode( string $shop_code ) : void;

	public function isActive() : bool;

	public function setIsActive( bool $is_active ) : void;

}
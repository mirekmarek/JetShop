<?php
namespace JetShop;

interface Core_CommonEntity_ShopDataInterface {

	public function getShopId() : string;

	public function setShopId( string $shop_id ) : void;

	public function isActive() : bool;

	public function setIsActive( bool $is_active ) : void;

}
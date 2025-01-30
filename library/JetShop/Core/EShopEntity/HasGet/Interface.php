<?php
namespace JetShop;


interface Core_EShopEntity_HasGet_Interface {
	
	public static function get( int|string $id ) : ?static;
}
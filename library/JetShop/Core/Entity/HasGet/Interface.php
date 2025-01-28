<?php
namespace JetShop;


interface Core_Entity_HasGet_Interface {
	
	public static function get( int|string $id ) : ?static;
}
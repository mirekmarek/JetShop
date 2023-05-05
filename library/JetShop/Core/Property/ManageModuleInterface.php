<?php
namespace JetShop;

interface Core_Property_ManageModuleInterface {
	
	public function getPropertyEditUrl( int $id ) : string;
	
	public function getPropertySelectWhispererUrl( string $only_type, bool $only_active=false ) : string;
	
}
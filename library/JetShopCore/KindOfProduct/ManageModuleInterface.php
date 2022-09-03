<?php
namespace JetShop;

interface Core_KindOfProduct_ManageModuleInterface {
	
	public function getKindsOfProductEditUrl( int $id ) : string;
	
	public function getKindOfProductSelectWhispererUrl( $only_active=false ) : string;
	
}
<?php
namespace JetShop;

use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_HasActivation_Interface;

interface Core_Admin_EntityManager_Interface {
	
	public static function getEntityInstance(): EShopEntity_Basic;
	
	public function showName( int|EShopEntity_Basic $id_or_item ) : string;
	
	public static function getEditUrl( int|EShopEntity_Basic $id_or_item, array $get_params=[] ) : string;
	
	public static function getCurrentUserCanEdit() : bool;
	public static function getCurrentUserCanCreate() : bool;
	public static function getCurrentUserCanDelete() : bool;
	
	public function renderActiveState( EShopEntity_HasActivation_Interface $item ) : string;
	
}
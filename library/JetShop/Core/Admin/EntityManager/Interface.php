<?php
namespace JetShop;

use JetApplication\Entity_Basic;
use JetApplication\Entity_HasActivation_Interface;

interface Core_Admin_EntityManager_Interface {
	
	public static function getEntityInstance(): Entity_Basic;
	
	public function showName( int|Entity_Basic $id_or_item ) : string;
	
	public static function getEditUrl( int|Entity_Basic $id_or_item, array $get_params=[] ) : string;
	
	public static function getCurrentUserCanEdit() : bool;
	public static function getCurrentUserCanCreate() : bool;
	public static function getCurrentUserCanDelete() : bool;
	
	public function renderActiveState( Entity_HasActivation_Interface $item ) : string;
	
}